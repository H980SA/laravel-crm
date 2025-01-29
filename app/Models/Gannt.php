<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gannt extends Model
{
    protected $fillable = [
        'etapa',
        'pipelineprocess',
        'start_date',
        'finish_date',
        'priority',
        'progreso',
        'id_licitacion',
        'nombre_licitacion'
    ];

    protected $appends = ["open"];

    public function getOpenAttribute()
    {
        return true;
    }
} 