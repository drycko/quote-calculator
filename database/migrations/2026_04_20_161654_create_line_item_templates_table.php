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
        Schema::create('line_item_templates', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('category'); // design | dev | plugin
            $table->string('template_type'); // web | manual | ilead

            $table->string('calculation_type'); // fixed | hourly | percentage | converted

            $table->decimal('default_rate', 10, 2)->nullable();
            $table->decimal('default_percentage', 5, 2)->nullable();

            $table->string('currency')->nullable();
            $table->decimal('conversion_rate', 10, 2)->nullable();

            $table->boolean('is_plugin')->default(false);

            $table->text('default_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_item_templates');
    }
};
