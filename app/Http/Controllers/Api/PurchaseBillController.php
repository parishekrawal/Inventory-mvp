<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PurchaseBill;
use App\Models\PurchaseItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;

class PurchaseBillController extends Controller
{
    public function index(){
        return response()->json([
            'data'=>PurchaseBill::with('vendor')->latest()->get()
        ]);
    }

    public function store(Request $request){
        DB::beginTransaction();

        try{
            $purchase_bill=PurchaseBill::create([
                'bill_no' => 'PB-'.time(),
                'vendor_id' => $request->vendor_id,
                'bill_date' => now(),
                'subtotal' => 0,
                'tax_total' => 0,
                'grand_total' => 0,
                'notes' => $request->notes
            ]);

            $subtotal=0;
            $tax_total=0;

            foreach($request->items as $item){
                $lineTotal=$item['price'] * $item['quantity'];
                $tax_amount=$lineTotal * $item['tax_percent'] / 100;
                
                PurchaseItem::create([
                    'purchase_bill_id' => $purchase_bill->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_amount' => $tax_amount,
                    'total' => $lineTotal + $tax_amount,
                ]);

                $subtotal += $lineTotal;
                $tax_total += $tax_amount;

                Product::where('id',$item['product_id'])->increment('current_stock',$item['quantity']);
            }

            $purchase_bill->update([
                'subtotal' => $subtotal,
                'tax_total' => $tax_total,
                'grand_total' => $subtotal + $tax_total
            ]);

            DB::commit();
            logActivity('Purchase Bill Created',$purchase_bill->bill_no);

            return response()->json(['messsage'=>'Purchase Bill Created'],200);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }

    public function show($id){
        return response()->json(['data'=>PurchaseBill::with(['vendor','items.product.tax'])->findOrFail($id)]);
    }

    public function pdf($id){
        $purchase_bill=PurchaseBill::with(['vendor','items.product'])->findOrFail($id);
        $setting=Setting::first();
        $pdf=Pdf::loadView('pdf.purchase',compact('purchase_bill','setting'));
        logActivity('Pdf Created',$purchase_bill->bill_no);
        return $pdf->download('Purchase-'.$purchase_bill->bill_no.'.pdf');
    }
}
