<?php

namespace Webkul\Lead\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class MetricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'etapa_licitacion'       => $this->faker->randomElement(['MUR no presenta oferta', 'El cliente declara en STAND BY', 'NO adjudicado']),
            'capacidad_financiera'   => $this->faker->numberBetween(1, 10), 
            'capacidad_tecnica'      => $this->faker->numberBetween(1, 10), 
            'inteligencia_precios'   => $this->faker->numberBetween(1, 10), 
            'experiencia_servicios'  => $this->faker->numberBetween(1, 10), 
            'reputacion_mur'         => $this->faker->numberBetween(1, 10), 
            'conocimiento_costos'    => $this->faker->numberBetween(1, 10), 
            'cumplimiento_norma'     => $this->faker->numberBetween(1, 10),
            'relacion_cliente'       => $this->faker->randomFloat(1,10), // Valores entre 0.00 y 1.00
            'innovacion'             => $this->faker->randomFloat(1,10), // Valores entre 0.00 y 1.00
            'probabilidad_exito'     => $this->faker->randomFloat(1,10), // Valores entre 0.00 y 1.00
            'estrategia'             => $this->faker->word, // Palabra aleatoria
            'monto_estimado'         => $this->faker->randomFloat(2, 1000, 100000000), // Valores entre 1,000 y 100,000,000
            'margen_bruto'           => $this->faker->randomFloat(2, 0, 100), // Valores entre 0.00 y 100.00
            'capex_estimado'         => $this->faker->randomFloat(2, 1000, 100000000), // Valore
            'lead_id'                => \Webkul\Lead\Models\Lead::factory(), 
        ];
    }
}
