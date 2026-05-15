<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ticket_replies')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildSqliteTable('support_tickets');

            return;
        }

        Schema::table('ticket_replies', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
            $table->foreign('ticket_id')->references('id')->on('support_tickets')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ticket_replies')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildSqliteTable();

            return;
        }

        Schema::table('ticket_replies', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);

            if (Schema::hasTable('tickets')) {
                $table->foreign('ticket_id')->references('id')->on('tickets')->cascadeOnDelete();
            }
        });
    }

    private function rebuildSqliteTable(?string $referencedTable = null): void
    {
        Schema::dropIfExists('ticket_replies_fixed');

        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('ticket_replies_fixed', function (Blueprint $table) use ($referencedTable) {
            $table->id();
            $ticketId = $table->foreignId('ticket_id');

            if ($referencedTable) {
                $ticketId->constrained($referencedTable)->cascadeOnDelete();
            }

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message');
            $table->boolean('is_staff')->default(false);
            $table->timestamps();
        });

        DB::statement('
            INSERT INTO ticket_replies_fixed (id, ticket_id, user_id, message, is_staff, created_at, updated_at)
            SELECT id, ticket_id, user_id, message, is_staff, created_at, updated_at FROM ticket_replies
        ');

        Schema::drop('ticket_replies');
        Schema::rename('ticket_replies_fixed', 'ticket_replies');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
