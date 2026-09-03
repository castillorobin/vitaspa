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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('user_id')->comment('Usuario/terapeuta que atendió')->constrained('users')->restrictOnDelete();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->integer('duration_minutes')->default(60);
            $table->enum('service', ['Combinado', 'Craneo', 'Pies', 'Espalda']);
            $table->enum('status', ['Pendiente', 'Completada', 'Cancelada'])->default('Pendiente');
            $table->decimal('price', 10, 2);
            $table->enum('payment_method', ['Efectivo', 'Tarjeta', 'Transferencia']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
