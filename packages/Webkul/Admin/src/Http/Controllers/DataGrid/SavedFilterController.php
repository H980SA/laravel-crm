<?php

namespace Webkul\Admin\Http\Controllers\DataGrid;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\DataGrid\Repositories\SavedFilterRepository;

class SavedFilterController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected SavedFilterRepository $savedFilterRepository) {}

    /**
     * Save filters to the database.
     */
    public function store()
    {
        $userId = auth()->guard()->user()->id;

        $this->validate(request(), [
            'name' => 'required|unique:datagrid_saved_filters,name,NULL,id,src,'.request('src').',user_id,'.$userId,
        ]);

        Event::dispatch('datagrid.saved_filter.create.before');

        $savedFilter = $this->savedFilterRepository->create([
            'user_id' => $userId,
            'name'    => request('name'),
            'src'     => request('src'),
            'applied' => request('applied'),
        ]);

        Event::dispatch('datagrid.saved_filter.create.after', $savedFilter);

        return response()->json([
            'data'    => $savedFilter,
            'message' => trans('admin::app.components.datagrid.toolbar.filter.saved-success'),
        ]);
    }

    /**
     * Retrieves the saved filters.
     */
    public function get()
    {
        // Verifica qué parámetros están llegando en la solicitud
        $src = request()->get('src');
        $userId = auth()->guard()->user()->id;

        // Imprime los valores para asegurarte de que sean correctos
        if (!$src) {
            return response()->json(['error' => 'El parámetro src está vacío'], 400);
        }

        if (!$userId) {
            return response()->json(['error' => 'No se pudo obtener el user_id'], 400);
        }

        // Intenta recuperar los filtros
        $savedFilters = $this->savedFilterRepository->findWhere([
            'src'     => $src,
            'user_id' => $userId,
        ]);

        // Imprime los resultados para asegurarte de que la consulta está funcionando
        if ($savedFilters->isEmpty()) {
            return response()->json(['error' => 'No se encontraron filtros guardados', 'src' => $src, 'user_id' => $userId], 404);
        }

        return response()->json(['data' => $savedFilters]);
    }

    /**
     * Update the saved filter.
     */
    public function update(int $id)
    {
        $userId = auth()->guard()->user()->id;

        $this->validate(request(), [
            'name' => 'required|unique:datagrid_saved_filters,name,'.$id.',id,src,'.request('src').',user_id,'.$userId,
        ]);

        $savedFilter = $this->savedFilterRepository->findOneWhere([
            'id'      => $id,
            'user_id' => auth()->guard()->user()->id,
        ]);

        if (! $savedFilter) {
            return response()->json([], 404);
        }

        Event::dispatch('datagrid.saved_filter.update.before', $id);

        $updatedFilter = $this->savedFilterRepository->update(request()->only([
            'name',
            'src',
            'applied',
        ]), $id);

        Event::dispatch('datagrid.saved_filter.update.after', $updatedFilter);

        return response()->json([
            'data'    => $updatedFilter,
            'message' => trans('admin::app.components.datagrid.toolbar.filter.updated-success'),
        ]);
    }

    /**
     * Delete the saved filter.
     */
    public function destroy(int $id)
    {
        Event::dispatch('datagrid.saved_filter.delete.before', $id);

        $success = $this->savedFilterRepository->deleteWhere([
            'id'      => $id,
            'user_id' => auth()->guard()->user()->id,
        ]);

        Event::dispatch('datagrid.saved_filter.delete.after', $id);

        if (! $success) {
            return response()->json([
                'message' => trans('admin::app.components.datagrid.toolbar.filter.delete-error'),
            ]);
        }

        return response()->json([
            'message' => trans('admin::app.components.datagrid.toolbar.filter.delete-success'),
        ]);
    }
}
