<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('loyalty_transactions', 'reference')) {
                $table->string('reference')->nullable()->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('loyalty_transactions', 'reference')) {
                $table->dropUnique(['reference']);
                $table->dropColumn('reference');
            }
        });
    }
};
