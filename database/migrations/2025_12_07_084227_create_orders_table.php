<?php

use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default(OrderStatus::PENDING->value);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index للبحث السريع
            $table->index('status');
            $table->index('user_id');
            $table->index('created_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
