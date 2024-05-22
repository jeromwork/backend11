<?php

namespace App\Services\ApiRequestQueryBuilders;

use Illuminate\Foundation\Http\FormRequest;

trait Paginable
{
    protected function pagination($query, FormRequest $request)
    {
        if ( !$request->input('all') ) {
            $offset = ($request->input('page') && $request->input('perPage')) ? ( $request->input('page') - 1 ) * $request->input('perPage') : 0;
            $limit = ($request->input('perPage')) ?? 10;
            $query->offset($offset)->limit($limit);
            $countItemsOnPage = ($request->input('perPage')) ?? 10; // Defaulting to 10 if not provided
            $query->getModel()->setPerPage($countItemsOnPage);
        } else {
            $query->getModel()->setPerPage(10000); // Set a large number if 'all' is set
        }

        return $query;
    }
}
