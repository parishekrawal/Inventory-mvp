<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    
    public function Invoice(){
        return $this->belongsTo(Invoice::class);
    }
}
