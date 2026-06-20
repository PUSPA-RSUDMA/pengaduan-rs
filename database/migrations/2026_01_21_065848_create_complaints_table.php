<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            
            // TAMBAHKAN INI (Supaya tau siapa yang lapor/User ID)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->date('date');
            $table->string('reporter_type');
            $table->string('reporter_name')->nullable();

            $table->foreignId('source_id')->constrained('sources');

            // $table->foreignId('category_id')->constrained('categories');

            $table->text('description');
            $table->text('answer');
            $table->string('grade');
            $table->string('unit_destination')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};