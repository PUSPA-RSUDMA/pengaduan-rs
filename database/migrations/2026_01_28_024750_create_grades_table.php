<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('grades', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Merah, Kuning, Hijau
        $table->string('color_class')->default('bg-secondary'); // Biar bisa atur warna badge
        $table->integer('sla_hours')->default(24);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
