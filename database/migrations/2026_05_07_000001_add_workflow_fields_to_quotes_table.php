<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('quote_number', 20)->nullable()->unique()->after('id');
            $table->string('status', 30)->default('draft')->after('template_type');
            $table->string('client_contact_name')->nullable()->after('client_name');
            $table->string('client_contact_email')->nullable()->after('client_contact_name');
        });

        DB::table('quotes')
            ->orderBy('id')
            ->select(['id', 'user_id', 'salesperson_name', 'salesperson_email'])
            ->get()
            ->each(function ($quote): void {
                $updates = [
                    'quote_number' => 'W-EST-' . str_pad((string) $quote->id, 4, '0', STR_PAD_LEFT),
                ];

                if ($quote->user_id === null) {
                    $updates['client_contact_name'] = $quote->salesperson_name;
                    $updates['client_contact_email'] = $quote->salesperson_email;
                    $updates['salesperson_name'] = null;
                    $updates['salesperson_email'] = null;
                }

                DB::table('quotes')->where('id', $quote->id)->update($updates);
            });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropUnique(['quote_number']);
            $table->dropColumn([
                'quote_number',
                'status',
                'client_contact_name',
                'client_contact_email',
            ]);
        });
    }
};
