<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckExpectedCloseDateAttribute extends Migration
{
    public function up()
    {
        // Verificar si el atributo ya existe
        $exists = DB::table('attributes')
            ->where('code', 'expected_close_date')
            ->where('entity_type', 'leads')
            ->exists();

        // Si no existe, lo insertamos
        if (!$exists) {
            DB::table('attributes')->insert([
                'code' => 'expected_close_date',
                'name' => 'Expected Close Date',
                'entity_type' => 'leads',
                'type' => 'date',
                'is_required' => 0,
                'is_unique' => 0,
                'quick_add' => 1,
                'is_user_defined' => 0,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down()
    {
        // No eliminamos el atributo en caso de rollback para evitar pérdida de datos
    }
} 