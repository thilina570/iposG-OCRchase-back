<?php

namespace App\Repositories;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;

class PurchaseInvoiceRepository
{
    public function getAll()
    {
        return PurchaseInvoice::orderBy('id', 'DESC')->get();
    }

    public function createWithItems(array $summaryFields, array $lineItems, string $filePath)
    {
        // Save the purchase invoice and its items
        $purchaseInvoice = PurchaseInvoice::create([
            'user_id'        => auth()->id() ?? 1,
            'invoice_number' => $summaryFields['invoice_number'],
            'total_amount'   => $summaryFields['total_amount'],
            'purchase_date'  => $summaryFields['purchase_date'],
            'file_path'      => $filePath,
        ]);

        if (!empty($lineItems)) {
            foreach ($lineItems as $item) {
                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $purchaseInvoice->id,
                    'item_name'           => $item['item_name'],
                    'quantity'            => $item['quantity'],
                    'price_per_unit'      => $item['price_per_unit'],
                    'total_price'         => $item['total_price'],
                ]);
            }
        }

        return $purchaseInvoice;
    }
}
