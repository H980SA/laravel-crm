<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('leads')) {
            throw new \Exception('La tabla leads no existe. Asegúrate de ejecutar primero la migración de leads.');
        }

        if (!Schema::hasTable('persons')) {
            throw new \Exception('La tabla persons no existe. Asegúrate de ejecutar primero la migración de persons.');
        }

        Schema::create('lead_persons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lead_id');
            $table->unsignedInteger('person_id');
            $table->timestamps();
        });

        // Agregar las claves foráneas después
        DB::statement('ALTER TABLE lead_persons ADD CONSTRAINT lead_persons_lead_id_foreign 
            FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE');
            
        DB::statement('ALTER TABLE lead_persons ADD CONSTRAINT lead_persons_person_id_foreign 
            FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE');

        // Agregar el índice único
        DB::statement('ALTER TABLE lead_persons ADD UNIQUE INDEX lead_person_unique (lead_id, person_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_persons');
    }
};