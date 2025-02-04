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
        // Array de opciones para cada select
        $selectOptions = [
            'estado' => [
                'Ganada',
                'En concurso', 
                'Prospección',
                'No Ganada',
                'Stand by',
                'Desierta',
                'No Presentada',
                'Retiro de oferta',
            ],
            'modalidad' => [
                'Directo',
                'Consorcio',
            ],
            'ingresos_previstos' => [
                'SI',
                'NO',
            ],
            'etapa_licitacion' => [
                'Prospección y/o Precalificación',
                'Recepción de invitación & Confirmar participación',
                'Visita técnica o Reunión informativa',
                'Formulación de Consultas/Absolución',
                'Elaborar propuesta técnica/económica',
                'El cliente evalúa propuesta',
                'Short List',
                'Adjudicado',
                'El cliente declara en STAND BY',
                'El cliente declara en DESIERTO',
                'MUR retira la oferta',
                'MUR no presenta oferta',
                'NO Adjudicado',
            ],
            'causa_perdida' => [
                'Capex limitado para la adquisición de equipos',
                'Falta de disponibilidad de equipos propios',
                'Limitaciones en el mantenimiento de equipos propios', 
                'Estrategia Corporativa de margenes (la propuesta quedo en el Short pero no fue la mejor)',
                'Precio no competitivo, fuera de las expectativas del cliente',
                'Falto analizar el mercado e inteligencia de pricing',
                'Bajo puntaje en la Propuesta técnica (no fue convincente)',
                'Desempeño histórico poco satisfactorio y no recomendable por otros clientes',
                'Falta de evaluación o control de calidad en procesos críticos',
                'Falta de Equipos/Herramientas/Infraestructura o Proyectos de innovación',
                'Incapacidad para negociar precios competitivos en la adquisición de materiales clave',
                'Falta de socios estrategicos para el suministro de partes  y materiales',
                'Falta de Recursos Humanos Especializados',
                'El área usuaria no elaboró la propuesta',
                'Personal técnico no capacitado',
                'No presentó los Requisitos de Seguridad y Medio Ambiente o fueron Insuficientes',
                'El cliente tiene conflictos con su entorno, lo cual incrementa el riesgo en el desarrollo del servicio',
                'Elevado indice de accidentabilidad o no presenta indicadores proactivos',
                'No aplica',
            ],
            'estrategia' => [
                'Fidelización (Continuidad de Servicio)',
                'Optimización de precios y Expandir la cartera de clientes',
                'Optimización de precios y Diversificación de Portafolio',
                'Alianzas y Asociaciones Estratégicas (perspectiva técnica)',
                'Cross-selling & Optimización de precios',
                'Propuesta de valor agregado social, uso de la tecnología e innovación',
                'Alianzas y Asociaciones Estrategicas (perspectiva RRSS)',
                'Propuesta de Garantías y postventa',
                'Modelo de contratos flexibles y personalizados',
                'Expandir la cartera de clientes',
                'Cross-selling',
            ],
        ];

        // Obtener los IDs de los atributos e insertar opciones
        foreach ($selectOptions as $code => $options) {
            $attributeId = DB::table('attributes')
                ->where([
                    'code' => $code,
                    'entity_type' => 'leads'
                ])
                ->value('id');

            if ($attributeId) {
                $sortOrder = 1;
                foreach ($options as $option) {
                    DB::table('attribute_options')->insert([
                        'attribute_id' => $attributeId,
                        'name' => $option,
                        'sort_order' => $sortOrder++
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Obtener los IDs de los atributos
        $attributeIds = DB::table('attributes')
            ->whereIn('code', [
                'estado',
                'modalidad',
                'ingresos_previstos',
                'etapa_licitacion',
                'causa_perdida',
                'estrategia'
            ])
            ->where('entity_type', 'leads')
            ->pluck('id');

        // Eliminar todas las opciones de estos atributos
        DB::table('attribute_options')
            ->whereIn('attribute_id', $attributeIds)
            ->delete();
    }
};