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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('code')->nullable(); 
            $table->string('press')->nullable(); 
            
            $table->integer('total_qty')->default(0);
            $table->integer('available_qty')->default(0);
            
            $table->string('cover_image')->nullable();             
            $table->longText('content')->nullable();  
            $table->longText('abstract')->nullable();     
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
