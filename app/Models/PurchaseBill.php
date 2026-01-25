<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseBill extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function items(){
        return $this->hasMany(PurchaseItem::class);
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }
    
    public function creditNotes(){
        return $this->hasMany(CreditNote::class);
    }
}
