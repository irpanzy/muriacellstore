<?php

namespace App\Services;

use App\Models\ProductTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\HandphoneRepositoryInterface;
use App\Repositories\Contracts\PromoCodeRepositoryInterface;

class OrderService
{
    protected $categoryRepository;
    protected $promoCodeRepository;
    protected $orderRepository;
    protected $handphoneRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        PromoCodeRepositoryInterface $promoCodeRepository,
        OrderRepositoryInterface $orderRepository,
        HandphoneRepositoryInterface $handphoneRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->promoCodeRepository = $promoCodeRepository;
        $this->orderRepository = $orderRepository;
        $this->handphoneRepository = $handphoneRepository;
    }

    public function beginOrder(array $data)
    {
        $orderData = [
            'handphone_capacity' => $data['handphone_capacity'],
            'capacity_id' => $data['capacity_id'],
            'handphone_id' => $data['handphone_id'],
        ];
        $this->orderRepository->saveToSession($orderData);
    }

    public function getOrderDetails()
    {
        $orderData = $this->orderRepository->getOrderDataFromSession();
        $handphone = $this->handphoneRepository->find($orderData['handphone_id']);

        $quantity = isset($orderData['quantity']) ? $orderData['quantity'] : 1;
        $subTotalAmount = $handphone->price * $quantity;

        $taxRate = 0;
        $totalTax = $subTotalAmount * $taxRate;

        $grandTotalAmount = $subTotalAmount + $totalTax;

        $orderData['sub_total_amount'] = $subTotalAmount;
        $orderData['total_tax'] = $totalTax;
        $orderData['grand_total_amount'] = $grandTotalAmount;

        return compact('orderData', 'handphone');
    }

    public function applyPromoCode(string $code, int $subTotalAmount)
    {
        $promo = $this->promoCodeRepository->findByCode($code);
        if ($promo) {
            $discount = $promo->discount_amount;
            $grandTotalAmount = $subTotalAmount - $discount;
            $promoCodeId = $promo->id;
            return ['discount' => $discount, 'grandTotalAmount' => $grandTotalAmount, 'promoCodeId' => $promoCodeId];
        }
        return ['error' => 'Kode promo tidak tersedia!'];
    }

    public function saveBookingTransaction(array $data)
    {
        $this->orderRepository->saveToSession($data);
    }

    public function updateCustomerData(array $data)
    {
        $this->orderRepository->updateSessionData($data);
    }

    public function paymentConfirm(array $validated)
    {
        $orderData = $this->orderRepository->getOrderDataFromSession();
        $productTransaction = null;

        try { // closure based transaction
            DB::transaction(function () use ($validated, &$productTransactionId, $orderData) {
                if (isset($validated['proof'])) {
                    $proofPath = $validated['proof']->store('proofs', 'public');
                    // buktitransfer.png
                    $validated['proof'] = $proofPath;
                }

                $validated['name'] = $orderData['name'];
                $validated['email'] = $orderData['email'];
                $validated['phone'] = $orderData['phone'];
                $validated['address'] = $orderData['address'];
                $validated['post_code'] = $orderData['post_code'];
                $validated['city'] = $orderData['city'];
                $validated['quantity'] = $orderData['quantity'];
                $validated['sub_total_amount'] = $orderData['sub_total_amount'];
                $validated['grand_total_amount'] = $orderData['grand_total_amount'];
                $validated['discount_amount'] = $orderData['discount_amount'];
                $validated['promo_code_id'] = $orderData['promo_code_id'];
                $validated['handphone_id'] = $orderData['handphone_id'];
                $validated['handphone_capacity'] = $orderData['handphone_capacity'];

                $validated['is_paid'] = false;
                
                $validated['booking_trx_id'] = ProductTransaction::generateUniqueTrxId();

                $newTransaction = $this->orderRepository->createTransaction($validated);

                $productTransactionId = $newTransaction->Id;
            });
        } catch (\Exception $e) {
            Log::error('Error in payment confirmation: ' . $e->getMessage());
            session()->flash('error', $e->getMessage());
            return null;
        }

        return $productTransactionId;
    }
}
