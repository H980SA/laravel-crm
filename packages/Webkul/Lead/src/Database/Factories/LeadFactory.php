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
        $stageId = $this->faker->numberBetween(8, 16);

       
        if ($stageId === 14) {
            $status = 'won';
        } elseif ($stageId === 15) {
            $status = 'lost';
        } else {
            $status = 'open';
        }
        
        return [
            'title'          => $this->faker->sentence,
            'description'    => $this->faker->paragraph,
            'lead_value'     => $this->faker->numberBetween(1000, 10000),
            'status'         => $status,
            'lost_reason'    => $this->faker->optional()->sentence,
            'expected_close_date' => $this->faker->date(),
            'start_date' => $this->faker->date(),
            'closed_at'      => null,  
            'user_id'        => 1,    
            'person_id'      => 1,    
            'lead_source_id' => 1,
            'lead_type_id'   => 1,
            'lead_pipeline_id' => 2,
            'lead_pipeline_stage_id' => $stageId,
        ];
    }
}
