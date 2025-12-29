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
        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade'); // كل تصميم يخص مستخدم واحد
            
            $table->json('name'); // {"en": "Summer Abaya", "ar": "عباية صيفية"}
            $table->json('description'); // {"en": "...", "ar": "..."}
            $table->decimal('price', 10, 2); // السعر
            $table->boolean('is_active')->default(true); // التصميم نشط أم لا
            $table->timestamps();
            $table->softDeletes(); // للحذف الناعم
            
            // Indexes للبحث السريع
            $table->index('user_id');
            $table->index('price');
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designs');
    }
};
