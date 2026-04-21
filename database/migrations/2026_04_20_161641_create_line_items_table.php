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
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained()->cascadeOnDelete();

            $table->string('name');

            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('quantity', 10, 2)->default(1);

            $table->string('calculation_type'); // fixed | hourly | percentage | converted

            $table->decimal('percentage_value', 5, 2)->nullable();

            $table->string('currency')->default('ZAR');
            $table->decimal('conversion_rate', 10, 2)->nullable();

            $table->boolean('is_plugin')->default(false);

            $table->text('notes')->nullable();
            $table->decimal('total', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_items');
    }
};
