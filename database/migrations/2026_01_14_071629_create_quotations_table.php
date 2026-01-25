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
        Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->string('quotation_no')->unique();
                $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
                $table->date('quotation_date');
                $table->decimal('subtotal',10,2)->default(0);
                $table->decimal('tax_total',10,2)->default(0);
                $table->decimal('discount',10,2)->default(0);
                $table->decimal('grand_total',10,2)->default(0);
                $table->enum('status',['draft','converted'])->default('draft');
                $table->text('notes')->nullable();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
