<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Gannt extends Model
{
    protected $table = 'gantts';

    protected $fillable = [
        'text',
        'start_date',
        'end_date',
        'duration',
        'progress',
        'priority',
        'is_parent',
        'parent_id',
        'lead_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'progress' => 'float',
        'is_parent' => 'boolean'
    ];

    protected $attributes = [
        'progress' => 0,
        'priority' => 'Media'
    ];

    protected $appends = ['open'];

    public function getOpenAttribute()
    {
        return true;
    }

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d H:i') : null;
    }

    public function getEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d H:i') : null;
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Lead\Models\Lead::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
} 