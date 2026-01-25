<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;

class InvoiceController extends Controller
{
    public function index(){
        return Invoice::with('customer')->latest()->get();
    }

    public function show($id){
        return Invoice::with(['customer','items.product.tax'])->findOrFail($id);
    }

    public function pdf($id){
        $invoice=Invoice::with(['customer','items.product'])->findOrFail($id);
        $setting=Setting::first();
        $pdf=Pdf::loadView('pdf.invoice',compact('invoice','setting'));
        logActivity('Pdf Created',$invoice->invoice_no);
        return $pdf->download('Invoice-'.$invoice->invoice_no.'.pdf');
    }
}
