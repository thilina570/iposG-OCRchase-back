<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoiceItem extends Model
{
    protected $table = 'purchase_invoice_items';

    protected $fillable = [
        'purchase_invoice_id',
        'item_name',
        'quantity',
        'price_per_unit',
        'total_price',
    ];

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

}
