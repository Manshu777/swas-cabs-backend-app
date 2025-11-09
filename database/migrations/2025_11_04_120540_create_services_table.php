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
        Schema::create('services', function (Blueprint $table) {
              $table->id();
    $table->string('type');                       
    $table->string('title');                  
    $table->text('description')->nullable();      
    $table->decimal('price', 10, 2)->default(0);
    $table->string('image')->nullable();          
    $table->boolean('is_active')->default(true);  
    $table->string('status')->default('pending'); 
    $table->string('address')->nullable();        
    $table->decimal('latitude', 10, 7)->nullable();  
    $table->decimal('longitude', 10, 7)->nullable(); 
    $table->string('contact_person')->nullable(); 
    $table->string('phone')->nullable();         
    $table->string('email')->nullable();         
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
