<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\PurchaseBill;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(){
        return response()->json(['data'=>Payment::with(['invoice','purchaseBill','customer','vendor'])->latest()->get()]);
    }

    public function store(Request $request){

        DB::beginTransaction();
        
        try{

            $payment=Payment::create([
                'payment_no'=>'PAY-'.time(),
                'type'=>$request['form']['type'],
                'invoice_id'=>$request['form']['invoice_id'] ?? null,
                'purchase_bill_id'=>$request['form']['purchase_bill_id'] ?? null,
                'customer_id'=>$request['invoice']['customer']['id'] ?? null,
                'vendor_id'=>$request['purchase']['vendor']['id'] ?? null,
                'amount'=>$request['form']['amount'],
                'mode'=>$request['form']['mode'],
                'payment_date'=>now(),
            ]);

            if($request['form']['type']==='sales'){
                $invoice=Invoice::findOrFail($request['form']['invoice_id']);

                $invoice->increment('paid_amount',$request['form']['amount']);

                $invoice->update([
                    'due_amount'=>$invoice->grand_total - $invoice->paid_amount,
                ]);
            }

            if($request['form']['type']==='purchase'){

                $bill=PurchaseBill::findOrFail($request['form']['purchase_bill_id']);

                $bill->increment('paid_amount',$request['form']['amount']);

                $bill->update([
                    'due_amount' =>$bill->grand_total - $bill->paid_amount
                ]);
            }

            DB::commit();

            logActivity('Payment done',$payment->payment_no);

            return response()->json(['message' => 'Payment saved successfully']);
        }catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }
}