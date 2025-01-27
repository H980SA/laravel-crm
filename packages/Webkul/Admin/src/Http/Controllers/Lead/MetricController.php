<?php

namespace Webkul\Admin\Http\Controllers\Lead;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\MetricRepository;
use Webkul\Admin\Http\Requests\MetricForm;
use Wbkul\Admin\Http\Controllers\Lead\LeadController;


class MetricController extends Controller
{
    /**
    * Constructor.
    */
    public function __construct(
        protected MetricRepository $metricRepository,
        protected LeadRepository $leadRepository,
    ) {}

     /**
     * Create a new metric for a specific lead.
     *
     * @param Request $request
     * @param int $leadId
     * @return JsonResponse
     */
    public function store(Request $request, int $leadId)
    {
        $validated = $request->validate([
            'etapa_licitacion'       => 'required',
            'capacidad_financiera'   => 'required|numeric|min:0|max:10',
            'capacidad_tecnica'      => 'required|numeric|min:0|max:10',
            'inteligencia_precios'   => 'required|numeric|min:0|max:10',
            'experiencia_servicios'  => 'required|numeric|min:0|max:10',
            'reputacion_mur'         => 'required|numeric|min:0|max:10',
            'conocimiento_costos'    => 'required|numeric|min:0|max:10',
            'cumplimiento_norma'     => 'required|numeric|min:0|max:10',
            'relacion_cliente'       => 'required|numeric|min:0|max:10',
            'innovacion'             => 'required|numeric|min:0|max:10',
        ]);
        
        $lead = $this->leadRepository->findOrFail($leadId);

        $etapaLicitacion = $validated['etapa_licitacion'];
        $capacidadFinanciera = $validated['capacidad_financiera'];
        $capacidadTecnica = $validated['capacidad_tecnica'];
        $inteligenciaPrecios = $validated['inteligencia_precios'];
        $experienciaServicios = $validated['experiencia_servicios'];
        $reputacionMur = $validated['reputacion_mur'];
        $conocimientoCostos = $validated['conocimiento_costos'];
        $cumplimientoNorma = $validated['cumplimiento_norma'];
        $relacionCliente = $validated['relacion_cliente'];
        $innovacion = $validated['innovacion'];

        $weights = [
            'capacidad_financiera'   => 12,
            'capacidad_tecnica'      => 10,
            'inteligencia_precios'   => 8,
            'experiencia_servicios'  => 12,
            'reputacion_mur'         => 8,
            'conocimiento_costos'    => 9,
            'cumplimiento_norma'     => 8,
            'relacion_cliente'       => 8,
            'innovacion'             => 8,
            'etapa_licitacion'       => 17, 
        ];

        $probabilidadExito = (
            ($etapaLicitacion * $weights['etapa_licitacion'] / 10)+
            ($capacidadFinanciera * $weights['capacidad_financiera'] / 10) +
            ($capacidadTecnica * $weights['capacidad_tecnica'] / 10) +
            ($inteligenciaPrecios* $weights['inteligencia_precios'] / 10) +
            ($experienciaServicios * $weights['experiencia_servicios'] / 10) +
            ($reputacionMur * $weights['reputacion_mur'] / 10) +
            ($conocimientoCostos * $weights['conocimiento_costos'] / 10) +
            ($cumplimientoNorma * $weights['cumplimiento_norma'] / 10) +
            ($relacionCliente * $weights['relacion_cliente'] / 10) +
            ($innovacion * $weights['innovacion'] / 10) 
        ) / 100;

        $metric = $this->metricRepository->create(array_merge($validated, [
            'lead_id' => $lead->id,'probabilidad_exito' => $probabilidadExito
        ]));

        $allMetrics = $this->metricRepository->findWhere(['lead_id' => $lead->id]);

        return response()->json([
            'message' => 'Metric created successfully.',
            'data'    => $allMetrics,
        ]);
    }

    /**
    * Update an existing metric for a specific lead.
    *
    * @param Request $request
    * @param int $leadId
    * @return JsonResponse
    */
    public function update(Request $request, int $leadId)
    {
        $validated = $request->validate([
            'etapa_licitacion'       => 'required',
            'capacidad_financiera'   => 'nullable|numeric|min:0|max:10',
            'capacidad_tecnica'      => 'nullable|numeric|min:0|max:10',
            'inteligencia_precios'   => 'nullable|numeric|min:0|max:10',
            'experiencia_servicios'  => 'nullable|numeric|min:0|max:10',
            'reputacion_mur'         => 'nullable|numeric|min:0|max:10',
            'conocimiento_costos'    => 'nullable|numeric|min:0|max:10',
            'cumplimiento_norma'     => 'nullable|numeric|min:0|max:10',
            'relacion_cliente'       => 'required|numeric|min:0|max:10',
            'innovacion'             => 'required|numeric|min:0|max:10',
        ]);

        $lead = $this->leadRepository->findOrFail($leadId);

        $etapaLicitacion = $validated['etapa_licitacion'];
        $capacidadFinanciera = $validated['capacidad_financiera'];
        $capacidadTecnica = $validated['capacidad_tecnica'];
        $inteligenciaPrecios = $validated['inteligencia_precios'];
        $experienciaServicios = $validated['experiencia_servicios'];
        $reputacionMur = $validated['reputacion_mur'];
        $conocimientoCostos = $validated['conocimiento_costos'];
        $cumplimientoNorma = $validated['cumplimiento_norma'];
        $relacionCliente = $validated['relacion_cliente'];
        $innovacion = $validated['innovacion'];

        $weights = [
            'capacidad_financiera'   => 12,
            'capacidad_tecnica'      => 10,
            'inteligencia_precios'   => 8,
            'experiencia_servicios'  => 12,
            'reputacion_mur'         => 8,
            'conocimiento_costos'    => 9,
            'cumplimiento_norma'     => 8,
            'relacion_cliente'       => 8,
            'innovacion'             => 8,
            'etapa_licitacion'       => 17, 
        ];

        $probabilidadExito = (
            ($etapaLicitacion * $weights['etapa_licitacion'] / 10)+
            ($capacidadFinanciera * $weights['capacidad_financiera'] / 10) +
            ($capacidadTecnica * $weights['capacidad_tecnica'] / 10) +
            ($inteligenciaPrecios* $weights['inteligencia_precios'] / 10) +
            ($experienciaServicios * $weights['experiencia_servicios'] / 10) +
            ($reputacionMur * $weights['reputacion_mur'] / 10) +
            ($conocimientoCostos * $weights['conocimiento_costos'] / 10) +
            ($cumplimientoNorma * $weights['cumplimiento_norma'] / 10) +
            ($relacionCliente * $weights['relacion_cliente'] / 10) +
            ($innovacion * $weights['innovacion'] / 10) 
        ) / 100;

        $metric = $this->metricRepository->findWhere(['lead_id' => $lead->id])->first();

        if (!$metric) {
            return response()->json([
                'message' => 'Metric not found for this lead.',
            ], 404);
        }

        $metric=$this->metricRepository->update(array_merge($validated,
        ['probabilidad_exito' => $probabilidadExito]), $metric->id);
       
        return response()->json([
            'message' => 'Metric created successfully.',
            'data'    => $metric,
        ]);
    }

}
