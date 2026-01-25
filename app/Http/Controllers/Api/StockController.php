<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockTransaction;
use App\Models\Product;
use App\Http\Requests\StockRequest;

class StockController extends Controller
{
    public function stockIn(StockRequest $request){
        $product=Product::findOrFail($request->product_id);

        $product->increment('current_stock',$request->quantity);

        StockTransaction::create([
            'product_id'=>$request->product_id,
            'type' => 'IN',
            'quantity' => $request->quantity,
            'reference_id' => $request->purchase_id ?? null,
            'reference_type' => 'purchase'
        ]);

        logActivity('Stock Added',$product->name);

        return response()->json(['message' => 'Stock added successfully','product' => $product]);
    }

    public function stockOut(StockRequest $request){
        $product=Product::findOrFail($request->product_id);

        if($product->current_stock < $request->quantity){
            return response()->json(['message'=>'Insufficient stock'],422);
        }

        $product->decrement('current_stock',$request->quantity);

        StockTransaction::create([
            'product_id'=>$request->product_id,
            'type' => 'OUT',
            'quantity' => $request->quantity,
            'reference_id' => $request->sale_id ?? null,
            'reference_type'=>'sale'
        ]);
        
        logActivity('Stock Reduced',$product->name);

        return response()->json(['message'=>'Stock reduced successfully','product'=>$product]);
    }

    public function ledger($product_id){
        $product=Product::with(['category','unit','tax'])->findOrFail($product_id);
        $ledger=StockTransaction::where('product_id',$product_id)->latest()->get();
        return response()->json(['product'=>$product,'ledger'=>$ledger]);
    }
}
