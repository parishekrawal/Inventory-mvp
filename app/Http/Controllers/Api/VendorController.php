<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index(){
        return response()->json(['data'=>Vendor::latest()->get()]);
    }

    public function store(Request $request){
        $vendor=Vendor::create($request->all());

        logActivity('Vendor Created',$vendor->name);
        return response()->json(['message'=>'vendor created']);
    }

    public function show($id){
        return response()->json(['data'=>Vendor::findOrFail($id)]);
    }

    public function update(Request $request,$id){
        $vendor=Vendor::findOrFail($id);
        $vendor->update($request->all());

        logActivity('Vendor Updated',$vendor->name);
        return response()->json(['message'=>'vendor updated']);
    }

    public function destroy($id){
        $vendor=Vendor::findOrFail($id);
        $vendor->delete();

        logActivity('Vendor Deleted',$vendor->name);
        return response()->json(['message'=>'vendor deleted']);
    }
}
