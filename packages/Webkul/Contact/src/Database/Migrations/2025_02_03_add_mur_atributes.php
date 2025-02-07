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
        // Actualizar el nombre del atributo existente
        DB::table('attributes')
            ->where([
                'code' => 'user_id',
                'entity_type' => 'leads'
            ])
            ->update([
                'name' => 'Línea de Negocio'
            ]);
        // cambiar tipo de valor de prospecto
        DB::table('attributes')
            ->where([   
                'code' => 'lead_value',
                'entity_type' => 'leads'
            ])
            ->update([
                'type' => 'text',
                'validation' => 'numeric',
                'name' => 'Valor de licitación (USD)',
                'sort_order' => 22
            ]);

        // Eliminar los atributos existentes si existen
        DB::table('attributes')
            ->where('entity_type', 'leads')
            ->whereIn('code', [
                'comercial',
                'expected_close_date',
                'closing_year',
                'estado',
                'contrato_servicio',
                'modalidad',
                'ingresos_previstos',
                'fecha_inicio_licitacion',
                'visita_campo',
                'presentacion_propuesta',
                'presentacion_consultas',
                'absolucion_consultas',
                'etapa_licitacion',
                'causa_perdida',
                'estrategia',
                'margen_bruto',
                'capex_estimado',
                'capex_financiar',
                'expected_close_date_mur'
            ])
            ->delete();

        // Array de nuevos atributos
        $attributes = [
            [
                'code' => 'comercial',
                'name' => 'EJECUTIVO COMERCIAL',
                'type' => 'lookup',
                'lookup_type' => 'persons',
                'sort_order' => 7,
            ],
            [
                'code' => 'closing_year',
                'name' => 'Año de Cierre',
                'type' => 'text',
                'validation' => 'numeric',
                'sort_order' => 8,
            ],
            [
                'code' => 'estado',
                'name' => 'Estado',
                'type' => 'select',
                'validation' => NULL,
                'sort_order' => 9,
            ],
            [
                'code' => 'contrato_servicio',
                'name' => 'Contrato/Servicio',
                'type' => 'text',
                'validation' => NULL,
                'sort_order' => 10,
            ],
            [
                'code' => 'modalidad',
                'name' => 'Modalidad',
                'type' => 'select',
                'validation' => NULL,
                'sort_order' => 11,
            ],
            [
                'code' => 'ingresos_previstos',
                'name' => 'Ingresos Previstos para este año?',
                'type' => 'select',
                'validation' => NULL,
                'sort_order' => 12,
            ],
            [
                'code' => 'fecha_inicio_licitacion',
                'name' => 'Fecha de Inicio de Licitación',
                'type' => 'date',
                'validation' => NULL,
                'sort_order' => 13,
            ],
            [
                'code' => 'visita_campo',
                'name' => 'Visita de Campo',
                'type' => 'date',
                'validation' => NULL,
                'sort_order' => 14,
            ],
            [
                'code' => 'presentacion_consultas',
                'name' => 'Presentación de Consultas',
                'type' => 'date',
                'validation' => NULL,
                'sort_order' => 15,
            ],
            [
                'code' => 'absolucion_consultas',
                'name' => 'Absolución de Consultas',
                'type' => 'date',
                'validation' => NULL,
                'sort_order' => 16,
            ],
            [
                'code' => 'presentacion_propuesta',
                'name' => 'Presentación de Propuesta',
                'type' => 'date',
                'validation' => NULL,
                'sort_order' => 17,
            ],
            [
                'code' => 'expected_close_date_mur',
                'name' => 'Fecha de Cierre de Licitación',
                'type' => 'date',
                'validation' => NULL,
                'sort_order' => 18,
            ],
            [
                'code' => 'etapa_licitacion',
                'name' => 'Etapa de Licitación',
                'type' => 'select',
                'validation' => NULL,
                'sort_order' => 19,
            ],
            [
                'code' => 'causa_perdida',
                'name' => 'Causa de Pérdida o No Presentación Oferta',
                'type' => 'select',
                'validation' => NULL,
                'sort_order' => 20,
            ],
            [
                'code' => 'estrategia',
                'name' => 'Estrategia',
                'type' => 'select',
                'validation' => NULL,
                'sort_order' => 21,
            ],
            [
                'code' => 'margen_bruto',
                'name' => 'Margen Bruto %',
                'type' => 'text',
                'validation' => 'numeric',
                'sort_order' => 23,
            ],
            [
                'code' => 'capex_estimado',
                'name' => 'CapEX Estimado (USD)',
                'type' => 'text',
                'validation' => 'numeric',
                'sort_order' => 24,
            ],
            [
                'code' => 'capex_financiar',
                'name' => 'CapEX a Financiar %',
                'type' => 'text',
                'validation' => 'numeric',
                'sort_order' => 25,
            ],
        ];

        // Insertar todos los nuevos atributos
        foreach ($attributes as $attribute) {
            DB::table('attributes')->insert(array_merge([
                'entity_type' => 'leads',
                'is_required' => 0,
                'is_unique' => 0,
                'is_user_defined' => 1,
                'quick_add' => 1,
                'lookup_type' => NULL,
                'created_at' => now(),
                'updated_at' => now(),
            ], $attribute));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el nombre del atributo de línea de negocio
        DB::table('attributes')
            ->where([
                'code' => 'user_id',
                'entity_type' => 'leads'
            ])
            ->update([
                'name' => 'Propietario de Ventas',
                'type' => 'select'
            ]);

        // Revertir nombre de fecha de cierre esperada
        DB::table('attributes')
            ->where([
                'code' => 'expected_close_date',
                'entity_type' => 'leads'
            ])
            ->update([
                'name' => 'Fecha de Cierre Esperada'
            ]);

        // Array de códigos de atributos a eliminar
        $attributeCodes = [
            'comercial',
            'closing_year',
            'estado',
            'contrato_servicio',
            'modalidad',
            'ingresos_previstos',
            'fecha_inicio_licitacion',
            'visita_campo',
            'presentacion_propuesta',
            'presentacion_consultas',
            'absolucion_consultas',
            'etapa_licitacion',
            'causa_perdida',
            'estrategia',
            'margen_bruto',
            'capex_estimado',
            'capex_financiar',
            'expected_close_date_mur'
        ];

        // Eliminar todos los atributos creados
        DB::table('attributes')
            ->where('entity_type', 'leads')
            ->whereIn('code', $attributeCodes)
            ->delete();
    }
};