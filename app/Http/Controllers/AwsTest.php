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
        $this->analyzeExpense('bill1.png');
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
        dd(response()->json($result->toArray()));
        return $result->toArray();
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
