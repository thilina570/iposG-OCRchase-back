<?php

namespace App\Repositories;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PurchaseInvoiceRepository
{
    public function getAll()
    {
        return PurchaseInvoice::orderBy('id', 'DESC')->get();
    }

    public function getAllPaginated(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = PurchaseInvoice::query();

        if ($search) {
            $query->where('invoice_number', 'like', '%' . $search . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
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

        return PurchaseInvoice::with('user','items')->findOrFail($purchaseInvoice->id) ;
    }

    public function getInvoice(int $id)
    {
        return PurchaseInvoice::with('user','items')->findOrFail($id);
    }
}
