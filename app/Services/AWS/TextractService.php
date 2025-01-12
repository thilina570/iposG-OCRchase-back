<?php

namespace App\Services\AWS;

use Aws\S3\S3Client;
use Aws\Textract\TextractClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class TextractService
{
    protected $textractClient;
    protected $s3Client;

    public function __construct()
    {
        $this->textractClient = new TextractClient([
            'version' => 'latest',
            'region'  => config('aws.region'),
            'credentials' => [
                'key'    => config('aws.key'),
                'secret' => config('aws.secret'),
            ],
        ]);

        $this->s3Client = new S3Client([
            'region'  => config('aws.region'),
            'version' => 'latest',
            'credentials' => [
                'key'    => config('aws.key'),
                'secret' => config('aws.secret'),
            ],
        ]);
    }

    public function analyzeExpenseDocument($filePath)
    {
        try {
            $file = fopen($filePath, 'r');
            $result = $this->textractClient->analyzeExpense([
                'Document' => [
                    'Bytes' => fread($file, filesize($filePath)),
                ],
            ]);
            fclose($file);

            return $result->toArray();
        } catch (AwsException $e) {
            Log::error('AWS Textract analyzeExpense error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function analyzeDocument($filePath)
    {
        try {
            $file = fopen($filePath, 'r');
            $result = $this->textractClient->analyzeDocument([
                'Document' => [
                    'Bytes' => fread($file, filesize($filePath)),
                ],
                'FeatureTypes' => ['FORMS'],
            ]);
            fclose($file);

            return $result->toArray();
        } catch (AwsException $e) {
            Log::error('AWS Textract analyzeDocument error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function extractSummaryFields(array $expenseResponse): array
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

    public function extractLineItems(array $expenseResponse): array
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

    public function uploadToS3($filePath, $bucket, $key)
    {
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $bucket,
                'Key'    => $key,
                'SourceFile' => $filePath,
            ]);
            Log::info("File uploaded successfully to S3: {$result['ObjectURL']}");
            return $result['ObjectURL'];
        } catch (AwsException $e) {
            Log::error('S3 upload error: ' . $e->getMessage());
            throw $e;
        }
    }
}
