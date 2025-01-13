<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use Illuminate\Http\Request;
use App\Services\AWS\TextractService;
use App\Services\PurchaseDataParser;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Log;
use App\Repositories\PurchaseInvoiceRepository;
use Illuminate\Support\Facades\Session;

class PurchaseInvoiceController extends Controller
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

    public function index()
    {
        $invoices = $this->purchaseInvoiceRepository->getAll();
        return view('pages.purchase.index', ['invoices' => $invoices]);
    }

    public function create()
    {
        return view('pages.purchase.create');
    }

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

            //save invoice id to session
            Session::put('invoiceId', $purchaseInvoice->id);

            return back()->with('success', 'File uploaded successfully.')->with('invoiceId', $purchaseInvoice->id);
        } catch (\Exception $e) {
            Log::error('Error storing purchase invoice: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create purchase invoice'], 500);
        }
    }

    public function show($id)
    {
        $invoice = PurchaseInvoice::with('user','items')->findOrFail($id);
        return view('pages.purchase.show', ['invoice' => $invoice]);
    }

}
