<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(){
        return response()->json(['data'=>Product::with(['category','unit','tax'])->get()]);
    }

    public function store(ProductRequest $request){
        $data=$request->all();
        
        if($request->hasFile('image')){
            $data['image'] = $request->file('image')->store('products','public');
        }

        $product=Product::create($data);
        logActivity('Product Created',$product->name);
        return response()->json(['message'=>'product created']);
    }

    public function show($id){
        return response()->json(['data'=>Product::with(['category','unit','tax'])->findOrFail($id)]);
    }

    public function update(ProductRequest $request, $id){
        $product=Product::findOrFail($id);
        $data=$request->all();

        if($request->hasFile('image')){
            if($product->image){
                $data['image']=$request->file('image')->store('products','public');
                Storage::disk('public')->delete($product->image);
            }
        }

        $product->update($data);
        logActivity('Product Updated',$product->name);
        return response()->json(['message'=>'Product updated']);
    }

    public function destroy($id){
        $product=Product::findOrFail($id);
        if($product->image){
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        logActivity('Product deleted',$product->name);
        return response()->json(['message'=>'product deleted']);
    }

    public function lowStock(){
        $products=Product::whereColumn('current_stock','<','min_stock')->get();
        return response()->json(['low_stock'=>$products]);
    }

    public function getQuantity($id,$type,$billId){
        $query = Product::query();

        if ($type === 'purchase') {
                $query->whereHas('purchaseItems', function ($q) use ($billId, $id) {
                    $q->where('purchase_bill_id', $billId)
                    ->where('product_id', $id);
                })->with(['purchaseItems' => function ($q) use ($billId, $id) {
                    $q->where('purchase_bill_id', $billId)
                    ->where('product_id', $id);
                }]);
            } else {
                $query->whereHas('invoiceItems', function ($q) use ($billId, $id) {
                    $q->where('invoice_id', $billId)
                    ->where('product_id', $id);
                })->with(['invoiceItems' => function ($q) use ($billId, $id) {
                    $q->where('invoice_id', $billId)
                    ->where('product_id', $id);
                }]);
            }

            $data = $query->first();

        $quantity = $type=='purchase' ? $data->purchaseItems->first()->quantity : $data->invoiceItems->first()->quantity;

        return response()->json(['quantity'=>$quantity]);
    }
}
