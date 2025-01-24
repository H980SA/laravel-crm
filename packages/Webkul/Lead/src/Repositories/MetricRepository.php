<?php

namespace Webkul\Lead\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Lead\Models\Metric;

class MetricRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Metric::class;
    }

    /**
     * Create a new metric.
     *
     * @param  array  $data
     * @return \Webkul\Lead\Contracts\Metric
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a metric.
     *
     * @param  array  $data
     * @param  int  $id
     * @return \Webkul\Lead\Contracts\Metric
     */
    public function update(array $data, $id)
    {
        $metric = $this->findOrFail($id);

        $metric->update($data);

        return $metric;
    }

}