<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use Illuminate\Http\Request;
use App\Services\AWS\TextractService;
use App\Services\PurchaseDataParser;
use Illuminate\Support\Facades\Log;
use App\Repositories\PurchaseInvoiceRepository;

class PurchaseInvoiceController extends Controller
{
    protected $textractService;
    protected $purchaseDataParser;
    protected PurchaseInvoiceRepository $purchaseInvoiceRepository;

    public function __construct(TextractService $textractService, PurchaseDataParser $purchaseDataParser, PurchaseInvoiceRepository $purchaseInvoiceRepository)
    {
        $this->textractService = $textractService;
        $this->purchaseDataParser = $purchaseDataParser;
        $this->purchaseInvoiceRepository = $purchaseInvoiceRepository;
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

            $file = $request->file('file')->store('uploads');
            $filePath = storage_path('app/private/' . $file);

            // Process the file using Textract service
            $expenseResponse = $this->textractService->analyzeExpenseDocument($filePath);

            // Extract and normalize data
            $summaryFields = $this->purchaseDataParser->normalizeInvoide(
                $this->textractService->extractSummaryFields($expenseResponse)
            );

            $lineItems = $this->purchaseDataParser->normalizeInvoiceItem(
                $this->textractService->extractLineItems($expenseResponse)
            );

            $this->purchaseInvoiceRepository->createWithItems($summaryFields, $lineItems);

            return back()->with('success', 'File uploaded successfully.')->with('path', $file);
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
