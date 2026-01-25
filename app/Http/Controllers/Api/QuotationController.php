<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class QuotationController extends Controller
{
    public function index(){
        return response()->json(['data'=>Quotation::with('customer')->latest()->get()]);
    }

    public function store(Request $request){
        DB::beginTransaction();

        try{
            $quotation=Quotation::create([
                'quotation_no' => 'QT-'.time(),
                'customer_id' => $request->customer_id,
                'quotation_date' => now(),
                'subtotal' => 0,
                'tax_total' => 0,
                'discount' => $request->discount ?? 0,
                'grand_total' => 0,
                'notes' => $request->notes
            ]);

            $subtotal=0;
            $taxTotal=0;

            foreach($request->items as $item){
                $product=Product::findOrFail($item['product_id']);

                $lineTotal=$item['quantity'] * $item['price'];
                $taxAmount=($lineTotal * $item['tax_percent']) / 100;


                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_percent' => $item['tax_percent'],
                    'tax_amount' => $taxAmount,
                    'total' => $lineTotal + $taxAmount
                ]);

                $subtotal += $lineTotal;
                $taxTotal += $taxAmount;
            }

            $quotation->update([
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => $subtotal + $taxTotal - $quotation->discount
            ]);

            DB::commit();
            logActivity('Quotation Created',$quotation->quotation_no);

            return response()->json(['message' => 'Quotation created']);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }

    public function show($id){
        return response()->json(['data' => Quotation::with(['customer','items.product'])->findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $quotation = Quotation::where('id', $id)->where('status', 'draft')->firstOrFail();

            $quotation->items()->delete();

            $subtotal = 0;
            $taxTotal = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                $lineTotal = $item['quantity'] * $item['price'];
                $taxAmount = ($lineTotal * $item['tax_percent']) / 100;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_percent' => $item['tax_percent'],
                    'tax_amount' => $taxAmount,
                    'total' => $lineTotal + $taxAmount
                ]);

                $subtotal += $lineTotal;
                $taxTotal += $taxAmount;
            }

            $quotation->update([
                'customer_id' => $request->customer_id,
                'discount' => $request->discount ?? 0,
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => $subtotal + $taxTotal - ($request->discount ?? 0),
                'notes' => $request->notes
            ]);

            DB::commit();
            logActivity('Quotation Updated',$quotation->quotation_no);

            return response()->json(['message' => 'Quotation updated']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function convert($id){
        DB::beginTransaction();

        try {
            $quotation = Quotation::with('items')->findOrFail($id);

            if ($quotation->status === 'converted') {
                return response()->json(['message' => 'Already converted'], 400);
            }

            $invoice = Invoice::create([
                'invoice_no'    => 'INV-' . time(),
                'customer_id'   => $quotation->customer_id,
                'quotation_id'  => $quotation->id,
                'invoice_date'  => now(),
                'subtotal'      => $quotation->subtotal,
                'tax_total'     => $quotation->tax_total,
                'discount'      => $quotation->discount,
                'grand_total'   => $quotation->grand_total,
            ]);

            foreach ($quotation->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->price,
                    'tax_amount' => $item->tax_amount,
                    'total'      => $item->total,
                ]);

                Product::where('id', $item->product_id)->decrement('current_stock', $item->quantity);
            }

            $quotation->update(['status' => 'converted']);

            DB::commit();

            logActivity('Quotation converted to invoice',$invoice->id);

            return response()->json([
                'message' => 'Converted to invoice',
                'invoice_id' => $invoice->id
            ]);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id){
        $quotation=Quotation::findOrFail($id)->delete();
        
        logActivity('Quotation Deleted',$quotation->quotation_no);
        return response()->json(['message'=>'Quotation deleted']);
    }

    public function pdf($id){
        $quotation = Quotation::with(['customer', 'items.product'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.quotation',['quotation' => $quotation]);
        logActivity('Pdf Created',$quotation->quotation_no);
        return $pdf->stream('quotation-'.$quotation->quotation_no.'.pdf');
    }
}
