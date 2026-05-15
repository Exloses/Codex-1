<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->change();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'guest_phone')) {
                $table->string('guest_phone')->nullable();
            }

            if (! Schema::hasColumn('orders', 'guest_address_line1')) {
                $table->string('guest_address_line1')->nullable();
            }

            if (! Schema::hasColumn('orders', 'guest_address_line2')) {
                $table->string('guest_address_line2')->nullable();
            }

            if (! Schema::hasColumn('orders', 'guest_city')) {
                $table->string('guest_city')->nullable();
            }

            if (! Schema::hasColumn('orders', 'guest_state')) {
                $table->string('guest_state')->nullable();
            }

            if (! Schema::hasColumn('orders', 'guest_postal_code')) {
                $table->string('guest_postal_code')->nullable();
            }

            if (! Schema::hasColumn('orders', 'guest_country')) {
                $table->string('guest_country', 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach ([
                'guest_phone',
                'guest_address_line1',
                'guest_address_line2',
                'guest_city',
                'guest_state',
                'guest_postal_code',
                'guest_country',
            ] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        if (Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable(false)->change();
                $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }
};
