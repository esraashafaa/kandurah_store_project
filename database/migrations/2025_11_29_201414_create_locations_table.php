<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('city', 100);           
            $table->string('area', 100);           
            $table->string('street', 150);         
            $table->string('house_number', 50);   
            $table->decimal('lat', 10, 8); 
            $table->decimal('lng', 11, 8);  
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        


            $table->index('user_id');     
            $table->index('city');         
            $table->index('is_default');   
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
