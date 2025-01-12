<p align="center"><a href="https://iposg.com/en-gb/index.html" target="_blank"><img src="https://iposg.com/en-gb/assets/images/logo.png" width="400" alt="Laravel Logo"></a></p>


# AWS Textract Integration

This project integrates **AWS Textract** using the `TextractService` class located in the `App\Services\AWS` namespace. It utilizes the `aws/aws-sdk-php` package to extract text and structured data, including tables, forms, and expense details, from scanned documents and images.

## TextractService Methods

- **`analyzeExpenseDocument($filePath)`**  
  Processes an expense document and extracts key financial information such as vendor names, amounts, dates, and detailed invoice line items, including product names, prices, and item codes.

- **`analyzeDocument($filePath)`**  
  Performs basic text extraction from a document. This method is primarily used for testing the Textract integration.

- **`extractSummaryFields($expenseResponse)`**  
  Parses the response from `analyzeExpenseDocument` to provide a more structured set of key-value pairs representing invoice metadata.

- **`extractLineItems($expenseResponse)`**  
  Parses the response from `analyzeExpenseDocument` to generate a more detailed key-value representation of invoice line items.

## Configuration

Ensure AWS credentials are correctly set up in your Laravel `.env` file using the following keys:

```bash
AWS_KEY=your-aws-access-key-id
AWS_SECRET=your-aws-secret-access-key
AWS_REGION=your-preferred-region
```

## Additional Configurations

In `config/textract.php`, specify the S3 bucket details. You can also enable `backup_to_s3` by setting it to `true` if you want to save files to S3 automatically.

## Useful Links

- **AWS Textract Documentation**: [AWS Textract API Reference](https://docs.aws.amazon.com/textract/latest/dg/what-is.html)
- **AWS SDK for PHP Documentation**: [AWS SDK for PHP](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/welcome.html)

