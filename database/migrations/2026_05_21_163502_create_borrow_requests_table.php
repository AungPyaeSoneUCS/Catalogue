<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrow_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            
            
            $table->enum('status', ['pending', 'borrowed', 'overdue', 'returned', 'canceled','lost','damage'])->default('pending');
            
            $table->timestamp('booking_at')->useCurrent(); 
            $table->timestamp('borrowed_at')->nullable();  
            $table->timestamp('due_at')->nullable();       
            $table->timestamp('returned_at')->nullable();  
            $table->integer('lost_fine')->default(0);
            $table->integer('damage_fine')->default(0);
            $table->integer('fine_amount')->default(0);    
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrow_requests');
    }
};