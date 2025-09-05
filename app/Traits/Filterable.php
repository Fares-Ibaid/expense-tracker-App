<?php


namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    protected function applyFilters(Request $request, Builder $query)
    {
        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->query('category'));
            });
        }

        // Filter by date range
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [$request->query('startDate'), $request->query('endDate')]);
        }

        // Filter by amount range
        if ($request->filled('min_amount') && $request->filled('max_amount')) {
            $query->whereBetween('amount', [$request->query('min_amount'), $request->query('max_amount')]);
        }
    }
}
