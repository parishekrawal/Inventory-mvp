<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Vendor;

class OutstandingController extends Controller
{
    public function customers()
    {
        $customers = Customer::with([
            'invoices.creditNotes',
            'payments'
        ])->get();

        $data = $customers->map(function ($customer) {

            $totalInvoice = $customer->invoices->sum('grand_total');

            $totalPaid = $customer->payments
                ->where('type', 'sales')
                ->sum('amount');

            $totalReturn = $customer->invoices
                ->flatMap->creditNotes
                ->where('type', 'sales')
                ->sum('total_amount');

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'total_invoice' => $totalInvoice,
                'total_paid' => $totalPaid,
                'total_return' => $totalReturn,
                'outstanding' => $totalInvoice - $totalPaid - $totalReturn
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function vendors()
    {
        $vendors = Vendor::with([
            'purchaseBills.creditNotes',
            'payments'
        ])->get();

        $data = $vendors->map(function ($vendor) {

            $totalPurchase = $vendor->purchaseBills->sum('grand_total');

            $totalPaid = $vendor->payments
                ->where('type', 'purchase')
                ->sum('amount');

            $totalReturn = $vendor->purchaseBills
                ->flatMap->creditNotes
                ->where('type', 'purchase')
                ->sum('total_amount');

            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'total_purchase' => $totalPurchase,
                'total_paid' => $totalPaid,
                'total_return' => $totalReturn,
                'outstanding' => $totalPurchase - $totalPaid - $totalReturn
            ];
        });

        return response()->json(['data' => $data]);
    }
}
