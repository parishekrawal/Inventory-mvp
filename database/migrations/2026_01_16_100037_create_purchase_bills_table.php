<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no')->nullable();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->date('bill_date');
            $table->decimal('subtotal',10,2)->default(0);
            $table->decimal('tax_total',10,2)->default(0);
            $table->decimal('grand_total',10,2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_bills');
    }
};
