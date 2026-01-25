<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditNote extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function items(){
        return $this->hasMany(CreditNoteItem::class);
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function purchaseBill(){
        return $this->belongsTo(PurchaseBill::class);
    }
}
