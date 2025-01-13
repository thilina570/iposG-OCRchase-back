<?php

return [

    /*
         |--------------------------------------------------------------------------
         | Backup to S3
         |--------------------------------------------------------------------------
         | Determines whether the processed document should be backed up to S3.
         | Available options: true, false
         */
    'backup_to_s3' => env('AWS_TEXTRACT_BACKUP_TO_S3', false),

    /*
         |--------------------------------------------------------------------------
         | S3 Backup location
         |--------------------------------------------------------------------------
         | Determines where to save to back up file
         */
    'backup_location' => env('AWS_BUCKET'),

];
