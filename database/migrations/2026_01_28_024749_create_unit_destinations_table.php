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
    Schema::create('unit_destinations', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Contoh: IGD, Poli Anak, Kasir
        $table->string('code')->nullable(); // Kode Unit (Opsional)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_destinations');
    }
};
