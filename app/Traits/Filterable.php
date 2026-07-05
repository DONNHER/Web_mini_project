<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    public function scopeApplyFilters(Builder $query, Request $request, array $searchableColumns = [])
    {
        // 1. Pagination per page
        $perPage = $request->get('per_page', 10);

        // 2. Global Search
        if ($request->filled('search')) {
            $term = $request->get('search');
            $query->where(function($q) use ($term, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    if (str_contains($column, '.')) {
                        [$relation, $relCol] = explode('.', $column);
                        $q->orWhereHas($relation, function($rq) use ($relCol, $term) {
                            $rq->where($relCol, 'like', "%{$term}%");
                        });
                    } else {
                        $q->orWhere($column, 'like', "%{$term}%");
                    }
                }
            });
        }

        // 3. Column Specific Search/Filters
        foreach ($request->all() as $key => $value) {
            if ($value !== null && in_array($key, $searchableColumns)) {
                $query->where($key, $value);
            }
        }

        // 4. Sorting
        if ($request->filled('sort_by')) {
            $direction = $request->get('sort_direction', 'asc');
            $query->orderBy($request->get('sort_by'), $direction);
        } else {
            $query->latest();
        }

        return $query;
    }
}
