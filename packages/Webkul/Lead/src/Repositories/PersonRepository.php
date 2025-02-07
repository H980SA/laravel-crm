<?php

namespace Webkul\Lead\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Contact\Models\Person;

class PersonRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Person::class;
    }

    /**
     * Search persons by name
     */
    public function searchByName($term)
    {
        return $this->model
            ->where('name', 'like', '%' . $term . '%')
            ->with(['emails'])
            ->get();
    }
} 