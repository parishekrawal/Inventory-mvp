<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Product;

class CreditNoteController extends Controller
{   
    public function index(){
        $creditNotes = CreditNote::with(['invoice.customer','purchaseBill.vendor'])->latest()->get();
        return response()->json(['data'=>$creditNotes]);
    }

    public function store(Request $request){

            DB::beginTransaction();

        try{
            $note=CreditNote::create([
                'invoice_id'=>$request->invoice_id,
                'purchase_bill_id'=>$request->purchase_bill_id,
                'note_no' => 'CN-'.time(),
                'type'=>$request->type,
                'return_date'=>now(),
                'total_amount'=>0
            ]);

            $totalAmount=0;

            foreach($request->items as $item){

                foreach($request->all()['products'] as $p){
                    $taxPercent=$p['tax']['percentage'];
                    $price=$request->type=='purchase' ? $p['purchase_price'] : $p['sale_price'];
                }

                if($item['quantity']>$item['maxQuantity']){
                    return response()->json(['error'=>'Quantity cannot exceed max quantity'],422);
                }

                $lineTotal=$price*$item['quantity'];
                $taxAmount=($lineTotal*$taxPercent) / 100;
                $total=$lineTotal+$taxAmount;

                CreditNoteItem::create([
                    'credit_note_id'=>$note->id,
                    'product_id'=>$item['product_id'],
                    'quantity'=>$item['quantity'],
                    'price'=> $price,
                    'tax_amount'=>$taxAmount,
                    'total'=>$total,
                ]);

                if($request->type==='sales'){
                    Product::where('id',$item['product_id'])->increment('current_stock',$item['quantity']);
                } else {
                    Product::where('id',$item['product_id'])->decrement('current_stock',$item['quantity']);
                }

                $totalAmount+=$total;
            }

            $note->update(['total_amount'=>$totalAmount]);

            DB::commit();

            logActivity('Credit Note Created',$note->note_no);

            return response()->json(['message'=>'Credit Note Created','id'=>$note->id],200);
        }catch(\Exception $e){
            return response(['error'=>$e->getMessage()],500);
        }
    }

    public function show($id){
        $creditNote = CreditNote::with(['invoice.customer','purchaseBill.vendor','items.product'])->findOrFail($id);

        return response()->json(['data' => $creditNote]);
    }
}
