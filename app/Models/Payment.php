<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function purchaseBill(){
        return $this->belongsTo(PurchaseBill::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }
}
