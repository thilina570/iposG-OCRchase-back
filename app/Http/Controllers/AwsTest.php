<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Aws\Textract\TextractClient;

class AwsTest extends Controller
{
    public function textTract()
    {
//        $this->analyzeDocument('bill1.png');
        $this->analyzeExpense('keels-2.jpg');
//        $this->uploadInvoice('bill1.png');
    }

    public function uploadInvoice($filePath)
    {
//        $request->validate([
//            'invoice' => 'required|file|mimes:jpeg,png,pdf',
//        ]);
//
//        $filePath = $request->file('invoice')->store('invoices', 'public');

        $expenseResponse = $this->analyzeExpenseDocument(storage_path("app/public/{$filePath}"));

        $summaryFields = $this->extractSummaryFields($expenseResponse);
        $lineItems = $this->extractLineItems($expenseResponse);

        dd($lineItems);
        return response()->json([
            'summary_fields' => $summaryFields,
            'line_items' => $lineItems,
        ]);
    }

    public function analyzeExpense($filePath)
    {
        $client = new TextractClient([
            'version' => 'latest',
            'region'  => config('aws.region'),
            'credentials' => [
                'key'    => config('aws.key'),
                'secret' => config('aws.secret'),
            ],
        ]);

        $file = fopen($filePath, 'r');
        $result = $client->analyzeExpense([
            'Document' => [
                'Bytes' => fread($file, filesize($filePath)),
            ],
        ]);

        fclose($file);

        $summaryFields = $this->extractSummaryFields($result);
        $lineItems = $this->extractLineItems($result);
        dd($lineItems);
        dd(response()->json($result->toArray()));
        return $result->toArray();
    }

    private function extractSummaryFields($expenseResponse)
    {
        $summaryFields = [];

        if (isset($expenseResponse['ExpenseDocuments'][0]['SummaryFields'])) {
            foreach ($expenseResponse['ExpenseDocuments'][0]['SummaryFields'] as $field) {
                $name = $field['Type']['Text'] ?? 'Unknown';
                $value = $field['ValueDetection']['Text'] ?? null;
                $summaryFields[$name] = $value;
            }
        }

        return $summaryFields;
    }

    private function extractLineItems($expenseResponse)
    {
        $lineItems = [];

        if (isset($expenseResponse['ExpenseDocuments'][0]['LineItemGroups'])) {
            foreach ($expenseResponse['ExpenseDocuments'][0]['LineItemGroups'] as $lineItemGroup) {
                foreach ($lineItemGroup['LineItems'] as $lineItem) {
                    $item = [];
                    foreach ($lineItem['LineItemExpenseFields'] as $field) {
                        $name = $field['Type']['Text'] ?? 'Unknown';
                        $value = $field['ValueDetection']['Text'] ?? null;
                        $item[$name] = $value;
                    }
                    $lineItems[] = $item;
                }
            }
        }

        return $lineItems;
    }
    public function analyzeDocument($filePath){
        $client = new TextractClient([
            'version' => 'latest',
            'region'  => config('aws.region'),
            'credentials' => [
                'key'    => config('aws.key'),
                'secret' => config('aws.secret'),
            ],
        ]);

        $file = fopen($filePath, 'r');
        $result = $client->analyzeDocument([
            'Document' => [
                'Bytes' => fread($file, filesize($filePath)),
            ],
            'FeatureTypes' => ['FORMS'],
        ]);

        fclose($file);
        dd(response()->json($result->toArray()));
        return $result->toArray();
    }
    public function s3Upload()
    {
        $s3 = new S3Client([
            'region'  => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $bucket = env('AWS_BUCKET');
        $filePath = 'file.txt';
        $key = 'file.txt';

        try {
            $result = $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $key,
                'SourceFile' => $filePath,
                // 'ACL'    => 'public-read', // Optional: Set file permissions
            ]);
            echo "File uploaded successfully. URL: " . $result['ObjectURL'];
        } catch (Aws\Exception\AwsException $e) {
            echo "Error uploading file: " . $e->getMessage();
        }
    }
}
