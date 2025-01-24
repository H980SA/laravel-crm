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
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->string('etapa_licitacion');
            $table->decimal('capacidad_financiera', 3, 2); // de 0-10
            $table->decimal('capacidad_tecnica', 3, 2);
            $table->decimal('inteligencia_precios', 3, 2);
            $table->decimal('experiencia_servicios', 3, 2);
            $table->decimal('reputacion_mur', 3, 2);
            $table->decimal('conocimiento_costos', 3, 2);
            $table->decimal('cumplimiento_norma', 3, 2);
            $table->decimal('relacion_cliente', 3, 2);
            $table->decimal('innovacion', 3, 2);
            $table->decimal('probabilidad_exito',3,2);
            $table->string('estrategia');
            $table->decimal('monto_estimado', 15, 2);
            $table->decimal('margen_bruto', 5, 2);
            $table->decimal('capex_estimado', 15, 2); 
            $table->integer('lead_id')->unsigned();
            $table->foreign('lead_id')->references('id')->on('leads');// Relación con la tabla leads
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
