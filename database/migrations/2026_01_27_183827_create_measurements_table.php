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
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('chest', 8, 2)->nullable()->comment('الصدر');
            $table->decimal('waist', 8, 2)->nullable()->comment('الخصر');
            $table->decimal('sleeve', 8, 2)->nullable()->comment('الأكمام');
            $table->decimal('shoulder', 8, 2)->nullable()->comment('الكتف');
            $table->decimal('hip', 8, 2)->nullable()->comment('الورك');
            $table->decimal('height', 8, 2)->nullable()->comment('الطول');
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
