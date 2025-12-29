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
        Schema::create('design_design_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->onDelete('cascade');
            $table->foreignId('design_option_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // منع التكرار
            $table->unique(['design_id', 'design_option_id']);
            
            // Indexes
            $table->index('design_id');
            $table->index('design_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_design_option');
    }
};
