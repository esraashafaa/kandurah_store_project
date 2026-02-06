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
        Schema::table('designs', function (Blueprint $table) {
            // إزالة foreign key constraint أولاً
            if (Schema::hasColumn('designs', 'measurement_id')) {
                $table->dropForeign(['measurement_id']);
                $table->dropColumn('measurement_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            // في حالة rollback، يمكن إعادة الحقل (لكن لا نحتاجه)
            // $table->foreignId('measurement_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
