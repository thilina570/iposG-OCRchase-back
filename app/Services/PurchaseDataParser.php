<?php

namespace App\Services;

class PurchaseDataParser
{

    public function normalizeInvoide(array $summaryFields): array
    {
        $normalizedData = [];
        $normalizedData['invoice_number'] = self::parseInvoiceNumber($summaryFields['INVOICE_RECEIPT_ID'] ?? '');
        $normalizedData['purchase_date'] = self::parseDate($summaryFields['INVOICE_RECEIPT_DATE'] ?? '');
        $normalizedData['total_amount'] = self::parsePrice($summaryFields['TOTAL'] ?? '');
        return $normalizedData;
    }

    public function normalizeInvoiceItem(array $lineItems): array
    {
        $normalizedData = [];

        foreach ($lineItems as $item) {
            $normalizedData[] = [
                'item_name' => self::parseString($item['ITEM'] ?? ''),
                'quantity' => self::parseQuantity($item['QUANTITY'] ?? ''),
                'price_per_unit' => self::parsePrice($item['UNIT_PRICE'] ?? ''),
                'total_price' => self::parsePrice($item['PRICE'] ?? ''),
            ];
        }

        return $normalizedData;
    }

    private static function parseInvoiceNumber(string $invoiceNumber): ?string
    {
        // If item name is null or empty, return an empty string
        if (empty($invoiceNumber)) {
            return '';
        }

        // Remove special characters but keep alphanumeric characters and spaces
        $cleanedItemName = preg_replace('/[^a-zA-Z0-9\s]/', '', $invoiceNumber);

        // Trim extra spaces and return
        return trim($cleanedItemName);
    }

    private static function parseString(string $string): ?string
    {
        // If item name is null or empty, return an empty string
        if (empty($string)) {
            return '';
        }

        // Remove special characters but keep alphanumeric characters and spaces
        $cleanedItemName = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);

        // Trim extra spaces and return
        return trim($cleanedItemName);
    }

    private static function parseDate(string $date): ?string
    {
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y'];
        foreach ($formats as $format) {
            $parsedDate = \DateTime::createFromFormat($format, $date);
            if ($parsedDate) {
                return $parsedDate->format('Y-m-d');
            }
        }
        return null;
    }

    private static function parseQuantity(string $Quantity): ?float
    {
        // If $quantity is null or an empty string, return 0.00
        if (empty($quantity)) {
            return 0.00;
        }

        // Remove any non-numeric characters except decimal point
        $cleanedQuantity = preg_replace('/[^\d.]/', '', $quantity);

        // Convert to float if valid, otherwise return 0.00
        return is_numeric($cleanedQuantity) ? (float) $cleanedQuantity : 0.00;
    }
    private static function parsePrice(string $price): ?float
    {
        //(float) str_replace(',', '', $item['PRICE'])
        // Remove any non-numeric characters except decimal point
        $cleanedPrice = preg_replace('/[^\d.]/', '', $price);

        // Convert to float if valid, otherwise return 0.00
        return is_numeric($cleanedPrice) ? (float) $cleanedPrice : 0.00;
    }
}
