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
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {"ar": "إدارة الطلبات الكاملة", "en": "Full Orders Management"}
            $table->json('description')->nullable(); // {"ar": "...", "en": "..."}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('permission_group_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_group_id')->constrained('permission_groups')->onDelete('cascade');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->unique(['permission_group_id', 'permission_id'], 'permission_group_permission_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_group_permission');
        Schema::dropIfExists('permission_groups');
    }
};
