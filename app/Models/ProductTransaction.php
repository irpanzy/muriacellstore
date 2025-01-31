<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'booking_trx_id',
        'city',
        'post_code',
        'address',
        'quantity',
        'sub_total_amount',
        'grand_total_amount',
        'discount_amount',
        'is_paid',
        'handphone_id',
        'handphone_capacity',
        'promo_code_id',
        'proof',
    ];

    public static function generateUniqueTrxId()
    {
        $prefix = 'MCS';
        do {
            $randomString = $prefix . mt_rand(1000, 9999); //MCS189
        } while (self::where('booking_trx_id', $randomString)->exists());

        return $randomString; //MCS156
    }

    public function handphone(): BelongsTo
    {
        return $this->belongsTo(Handphone::class, 'handphone_id');
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }
}
