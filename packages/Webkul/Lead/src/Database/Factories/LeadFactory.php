<?php

namespace Webkul\Lead\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Lead\Models\Lead;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Webkul\Lead\Models\Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'title'          => $this->faker->sentence,
            'description'    => $this->faker->paragraph,
            'lead_value'     => $this->faker->numberBetween(1000, 10000),
            'status'         => $this->faker->randomElement(['open', 'won', 'lost']),
            'lost_reason'    => $this->faker->optional()->sentence,
            'expected_close_date' => $this->faker->date(),
            'start_date' => $this->faker->date(),
            'closed_at'      => null,  // o $this->faker->dateTime() según tu lógica
            'user_id'        => 1,     // o un user factory si lo tienes
            'person_id'      => 1,     // o algo dinámico
            'lead_source_id' => 1,
            'lead_type_id'   => 1,
            'lead_pipeline_id' => 1,
            'lead_pipeline_stage_id' => 1,
        ];
    }
}
