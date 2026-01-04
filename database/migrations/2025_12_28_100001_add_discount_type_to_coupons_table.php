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
        Schema::table('coupons', function (Blueprint $table) {
            // إضافة نوع الخصم: 'percentage' أو 'fixed'
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage')->after('code');
        });
        
        // تغيير discount ليستوعب قيم أكبر (للمبالغ الثابتة)
        // نستخدم DB::statement لأن change() قد لا يعمل مع بعض أنواع الأعمدة
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE coupons MODIFY discount DECIMAL(10, 2)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('discount_type');
        });
        
        // إعادة discount إلى الحجم الأصلي
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE coupons MODIFY discount DECIMAL(5, 2)');
    }
};

