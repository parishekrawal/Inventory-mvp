<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CustomerRequest;

class CustomerController extends Controller
{
    public function index(){
        return response()->json(['data'=>Customer::latest()->get()]);
    }

    public function store(CustomerRequest $request){
        $customer=Customer::create([...$request->all(),'created_by'=>Auth::id()]);

        logActivity('Customer Created',$customer->name);
        return response()->json(['message'=>'customer created']);
    }

    public function show($id){
        return Customer::findOrFail($id);
    }

    public function update(CustomerRequest $request,$id){
        $customer=Customer::findOrFail($id);
        $customer->update($request->all());

        logActivity('Customer Updated',$customer->name);
        return response()->json(['message'=>'customer updated']);
    }

    public function destroy($id){
        $customer=Customer::findOrFail($id);
        $customer->delete();

        logActivity('Customer Deleted',$customer->name);
        return response()->json(['message'=>'customer deleted']);
    }
}
