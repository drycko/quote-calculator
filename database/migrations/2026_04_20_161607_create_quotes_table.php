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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('client_name')->nullable();
            $table->string('salesperson_name')->nullable();
            $table->string('salesperson_email')->nullable();

            $table->string('template_type')->default('web'); // web | manual | ilead

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('main_total', 10, 2)->default(0);
            $table->decimal('plugin_total', 10, 2)->default(0);

            $table->decimal('markup_rate', 5, 2)->default(0);
            $table->decimal('markup_amount', 10, 2)->default(0);

            $table->decimal('total_ex_vat', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total_inc_vat', 10, 2)->default(0);

            $table->json('snapshot')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
