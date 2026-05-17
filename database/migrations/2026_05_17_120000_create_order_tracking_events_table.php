<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dropship_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 60);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('occurred_at');
            $table->string('source', 60)->default('system');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'occurred_at']);
            $table->index(['dropship_order_id', 'occurred_at']);
            $table->index(['status', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_tracking_events');
    }
};
