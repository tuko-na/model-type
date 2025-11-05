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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('model_number')->index(); 
            
            $table->string('name');
            $table->string('manufacturer')->nullable();
            $table->string('category')->nullable();
            
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expires_on')->nullable();
            
            $table->integer('price')->nullable();
            
            $table->string('manual_url')->nullable();
            $table->string('support_url')->nullable();
            
            $table->string('status')->nullable();
            $table->text('notes')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};