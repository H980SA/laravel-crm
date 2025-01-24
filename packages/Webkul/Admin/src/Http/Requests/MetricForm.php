<?php
namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MetricForm extends FormRequest
{
    public function rules(): array
    {
        return [
            'capacidad_financiera'   => 'required|numeric|min:0|max:10',
            'capacidad_tecnica'      => 'required|numeric|min:0|max:10',
            'inteligencia_precios'   => 'required|numeric|min:0|max:10',
            'experiencia_servicios'  => 'required|numeric|min:0|max:10',
            'reputacion_mur'         => 'required|numeric|min:0|max:10',
            'conocimiento_costos'    => 'required|numeric|min:0|max:10',
            'cumplimiento_norma'     => 'required|numeric|min:0|max:10',
        ];
    }
}