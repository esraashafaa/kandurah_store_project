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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('discount', 5, 2); // نسبة الخصم (0-100)
            $table->date('expires_at')->nullable(); // تاريخ الانتهاء
            $table->boolean('is_active')->default(true); // حالة التفعيل
            $table->integer('max_usage')->nullable(); // الحد الأقصى لعدد مرات الاستخدام
            $table->integer('usage_count')->default(0); // عدد مرات الاستخدام الحالي
            $table->decimal('min_purchase', 10, 2)->nullable(); // الحد الأدنى لمبلغ الشراء
            $table->text('description')->nullable(); // وصف الكوبون
            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
