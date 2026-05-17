<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('return_requests', 'refund_reference')) {
                $table->string('refund_reference')->nullable()->after('refund_amount_usd');
            }

            if (! Schema::hasColumn('return_requests', 'refund_processed_at')) {
                $table->timestamp('refund_processed_at')->nullable()->after('refund_reference');
            }

            if (! Schema::hasColumn('return_requests', 'refund_error')) {
                $table->text('refund_error')->nullable()->after('refund_processed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            foreach (['refund_error', 'refund_processed_at', 'refund_reference'] as $column) {
                if (Schema::hasColumn('return_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
