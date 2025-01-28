<?php

namespace Webkul\Lead\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    protected $fillable = [
        'etapa_licitacion',
        'capacidad_financiera',
        'capacidad_tecnica',
        'inteligencia_precios',
        'experiencia_servicios',
        'reputacion_mur',
        'conocimiento_costos',
        'cumplimiento_norma',
        'relacion_cliente',       
        'innovacion',         
        'probabilidad_exito', 
        'lead_id',
    ];


    /**
     * Get the lead that owns the lead product.
     */
    public function lead()
    {
        return $this->belongsTo(LeadProxy::modelClass());
    }


}
