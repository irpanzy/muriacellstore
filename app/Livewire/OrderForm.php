<?php

namespace App\Livewire;

use App\Models\Handphone;
use App\Services\OrderService;
use Livewire\Component;

class OrderForm extends Component
{
    public Handphone $handphone;
    public $orderData;
    public $subTotalAmount;
    public $promoCode = null;
    public $promoCodeId = null;
    public $quantity = 1;
    public $discount = 0;
    public $grandTotalAmount;
    public $totalDiscountAmount = 0;
    public $name;
    public $email;

    protected $orderService;

    public function boot(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function mount(Handphone $handphone, $orderData)
    {
        $this->handphone = $handphone;
        $this->orderData = $orderData;
        $this->subTotalAmount = $handphone->price;
        $this->grandTotalAmount = $handphone->price;
    }

    public function updatedQuantity()
    {
        $this->validateOnly(
            'quantity',
            [
                'quantity' => 'required|integer|min:1|max:',
            ],
            [
                'quantity.max' => 'Stock not available!',
            ]
        );
        $this->calculateTotal();
    }

    protected function calculateTotal(): void
    {
        $this->subTotalAmount = $this->handphone->price * $this->quantity;
        $this->grandTotalAmount = $this->subTotalAmount - $this->discount;
    }

    public function incrementQuantity()
    {
        if ($this->quantity < $this->handphone->stock) {
            $this->quantity++;
            $this->calculateTotal();
        }
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
            $this->calculateTotal();
        }
    }

    public function updatedPromoCode()
    {
        $this->applyPromoCode();
    }

    public function applyPromoCode()
    {
        if (!$this->promoCode) {
            $this->resetDiscount();
            return;
        }

        $result = $this->orderService->applyPromoCode($this->promoCode, $this->subTotalAmount);

        if (isset($result['error'])) {
            session()->flash('error', $result['error']);
            $this->resetDiscount();
        } else {
            session()->flash('message', 'Kode promo tersedia, yay!');
            $this->discount = $result['discount'];
            $this->calculateTotal();
            $this->promoCodeId = $result['promoCodeId'];
            $this->totalDiscountAmount = $result['discount'];
        }
    }

    protected function resetDiscount()
    {
        $this->discount = 0;
        $this->calculateTotal();
        $this->promoCodeId = null;
        $this->totalDiscountAmount = 0;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'quantity' => 'required|integer|min:1|max:' . $this->handphone->stock,
        ];
    }

    public function gatherBookingData(array $validatedData): array
    {
        return [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'quantity' => $this->quantity,
            'promo_code' => $this->promoCode,
            'promo_code_id' => $this->promoCodeId,
            'discount' => $this->discount,
            'sub_total_amount' => $this->subTotalAmount,
            'total_discount_amount' => $this->totalDiscountAmount,
            'grand_total_amount' => $this->grandTotalAmount,
        ];
    }

    public function submit()
    {
        $validatedData = $this->validate();
        $bookingData = $this->gatherBookingData($validatedData);
        $this->orderService->updateCustomerData($bookingData);

        return redirect()->route('front.customer_data');
    }

    public function render()
    {
        return view('livewire.order-form');
    } 
}
