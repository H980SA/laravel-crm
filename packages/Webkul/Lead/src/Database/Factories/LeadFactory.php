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
        $stageId = $this->faker->randomElement([8, 9, 10, 11, 12, 13, 16]);
        $leadType = $this->faker->randomElement([1, 4, 5, 6, 7, 8, 9]);
       
        
        
        return [
            'title'          => $this->faker->sentence,
            'description'    => $this->faker->paragraph,
            'lead_value'     => $this->faker->numberBetween(1000, 10000),
            'status'         => 'open',
            'lost_reason'    => $this->faker->optional()->sentence,
            'expected_close_date' => $this->faker->date(),
            'start_date' => $this->faker->date(),
            'closed_at'      => null,  
            'user_id'        => 1,    
            'person_id'      => 1,    
            'lead_source_id' => $this->faker->numberBetween(1, 3),
            'lead_type_id'   => $leadType,
            'lead_pipeline_id' => 2,
            'lead_pipeline_stage_id' => $stageId,
        ];
    }
}
