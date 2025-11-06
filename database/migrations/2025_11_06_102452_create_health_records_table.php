<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();

            // Kunci Asing
            $table->foreignId('sheep_id')->constrained('sheep')->onDelete('cascade');
            $table->foreignId('handler_id')->constrained('users')->onDelete('restrict')->comment('Petugas yang menangani kasus');

            // Atribut
            $table->date('record_date');
            $table->string('diagnosis')->nullable(); // Diagnosis (misal: "Pneumonia", "Cacingan")
            $table->text('treatment_details'); // Detail obat atau tindakan yang diberikan
            $table->string('medication_used')->nullable();
            $table->string('photo_path')->nullable(); // Foto kondisi/luka

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
