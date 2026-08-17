<?php

namespace App\Core\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTagihanBilling extends Model
{
    protected $table = 'billing_detail_tagihan';

    protected $fillable = [
        'billing_tagihan_id',
        'deskripsi',
        'qty',
        'harga',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(
            TagihanBilling::class,
            'billing_tagihan_id'
        );
    }
}