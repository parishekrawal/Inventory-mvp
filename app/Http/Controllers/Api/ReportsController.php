<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\PurchaseBill;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function stockSummary(){
        $products=Product::select('id','name','current_stock')->get();
        return response()->json(['data'=>$products]);
    }

    public function sales(){
        $sales=Invoice::select('customer_id',DB::raw('SUM(grand_total) as total_sales'))
                        ->with('customer:id,name')
                        ->groupBy('customer_id')
                        ->get()
                        ->map(fn($i)=>[
                            'name'=>$i->customer->name,
                            'total_sales'=>$i->total_sales
                        ]);
        return response()->json(['data'=>$sales]);
    }

    public function purchases(){
        $purchases = PurchaseBill::select('vendor_id', DB::raw('SUM(grand_total) as total_purchase'))
                        ->with('vendor:id,name')
                        ->groupBy('vendor_id')
                        ->get()
                        ->map(fn($i)=>[
                            'name'=>$i->vendor->name,
                            'total_purchase'=>$i->total_purchase
                        ]);
        return response()->json(['data'=>$purchases]);
    }

    public function profitLoss(){
        $cost = Invoice::with('items.product')->get()
                ->flatMap(fn ($invoice) => $invoice->items)
                ->reduce(fn ($sum, $item) => $sum + ($item->product->purchase_price * $item->quantity),0);
        $revenue = Invoice::sum('grand_total');
        $profit = $revenue - $cost;
        return response()->json(['data'=>['revenue'=>$revenue,'cost'=>$cost,'profit'=>$profit]]);
    }
}
