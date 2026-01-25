<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function items(){
        return $this->hasMany(InvoiceItem::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function quotation(){
        return $this->belongsTo(Quotation::class);
    }

    public function creditNotes() {
        return $this->hasMany(CreditNote::class);
    }
}
