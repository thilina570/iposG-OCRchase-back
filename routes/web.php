<?php

use Illuminate\Support\Facades\Route;
use Aws\S3\S3Client;

Route::get('/', function () {
    // return view('welcome');

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

});
