<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Repositories\PurchaseInvoiceRepository;
use App\Services\AWS\TextractService;
use App\Services\FileUploadService;
use App\Services\PurchaseDataParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PurchaseInvoiceControllerV2 extends Controller
{
    protected $textractService;
    protected $purchaseDataParser;
    protected $fileUploadService;
    protected PurchaseInvoiceRepository $purchaseInvoiceRepository;

    public function __construct(
        TextractService $textractService,
        PurchaseDataParser $purchaseDataParser,
        FileUploadService $fileUploadService,
        PurchaseInvoiceRepository $purchaseInvoiceRepository
    )
    {
        $this->textractService              = $textractService;
        $this->purchaseDataParser           = $purchaseDataParser;
        $this->fileUploadService            = $fileUploadService;
        $this->purchaseInvoiceRepository    = $purchaseInvoiceRepository;
    }

    /**
     * Display a listing of the purchase invoices.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15); // Default to 15 per page

        $validated = $request->validate([
            'search'    => 'nullable|string|max:255',
            'per_page'  => 'nullable|integer|min:1|max:100',
        ]);

        try {
            $invoices = $this->purchaseInvoiceRepository->getAllPaginated($validated['search'] ?? null, $validated['per_page'] ?? 15);

            return response()->json([
                'success' => true,
                'data'    => [
                    'current_page' => $invoices->currentPage(),
                    'per_page'     => $invoices->perPage(),
                    'total'        => $invoices->total(),
                    'last_page'    => $invoices->lastPage(),
                    'data'         => $invoices->items(),
                ],
            ], Response::HTTP_OK, [
                'Content-Type' => 'application/json',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch purchase invoices: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve purchase invoices.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR, [
                'Content-Type' => 'application/json',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $invoices = PurchaseInvoice::all();
        return response()
            ->json([
                'success' => true,
                'data'    => $invoices,
            ], Response::HTTP_OK, [
                'Content-Type' => 'application/json',
                'X-Content-Type-Options' => 'nosniff',
            ]);
    }

    /**
     * Store a newly created purchase invoice in storage.
     * Accepts a file upload (PDF or image).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate the uploaded file
            $request->validate([
                'file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048', // 2MB max
            ]);

            $file = $request->file('file');

            // Store the file
            $path = $this->fileUploadService->storeFile($file, 'uploads');

            // Upload file to S3
            if(config('textract.backup_to_s3')){
                $s3Url = $this->textractService->uploadToS3($file, config('textract.backup_location'), $path);
            }

            // Process the file using Textract service
            $expenseResponse = $this->textractService->analyzeExpenseDocument($file);

            // Extract and normalize data
            $summaryFields = $this->purchaseDataParser->normalizeInvoide(
                $this->textractService->extractSummaryFields($expenseResponse)
            );

            // Normalize purchase invoice data
            $lineItems = $this->purchaseDataParser->normalizeInvoiceItem(
                $this->textractService->extractLineItems($expenseResponse)
            );

            // Save invoice
            $purchaseInvoice = $this->purchaseInvoiceRepository->createWithItems($summaryFields, $lineItems,$path);

            return response()
                ->json([
                    'success' => true,
                    'message' => 'Purchase Invoice created successfully',
                    'data'    => $purchaseInvoice,
                ], Response::HTTP_CREATED, [
                    'Content-Type' => 'application/json',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
        } catch (\Exception $e) {
            Log::error('Error storing purchase invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create purchase invoice',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR, [
                'Content-Type'           => 'application/json',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }
    }

    /**
     * Display the specified purchase invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $invoice = $this->purchaseInvoiceRepository->getInvoice($id);
        if (! $invoice) {
            return response()
                ->json([
                    'success' => false,
                    'message' => 'Purchase Invoice not found',
                ], Response::HTTP_NOT_FOUND, [
                    'Content-Type' => 'application/json',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
        }
        return response()
            ->json([
                'success' => true,
                'data'    => $invoice,
            ], Response::HTTP_OK, [
                'Content-Type' => 'application/json',
                'X-Content-Type-Options' => 'nosniff',
            ]);
    }
}
