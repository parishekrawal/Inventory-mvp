<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tax;

class TaxController extends Controller
{
    public function index(){
        return response()->json(['data'=>Tax::latest()->get()]);
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required|unique:taxes,name',
            'percentage'=>'required|numeric|min:0|max:100'
        ]);

        $tax=Tax::create($request->all());

        logActivity('Tax Created',$tax->name);
        return response()->json(['message'=>'Tax added successfully']);
    }

    public function update(Request $request,$id){
        $tax=Tax::findOrFail($id);

        $request->validate([
            'name'=>'required|unique:taxes,name,'.$tax->id,
            'percentage'=>'required|numeric|min:0,max:100'
        ]);
        $tax->update($request->all());

        logActivity('Tax Updated',$tax->name);
        return response()->json(['message'=>'Tax updated successfully']);
    }

    public function destroy($id){
        $tax=Tax::findOrFail($id);
        $tax->delete();

        logActivity('Tax Deleted',$tax->name);
        return response()->json(['message'=>'Tax deleted successfully']);
    }
}
