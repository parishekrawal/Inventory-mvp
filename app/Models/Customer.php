<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function invoices(){
        return $this->hasMany(Invoice::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }
}
