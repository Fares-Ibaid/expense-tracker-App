<?php


namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait Filterable
{
    protected function applyFilters(Request $request, Builder $query)
    {

       // dd($request->all());
    // Filter by category
    if ($request->filled('category')) {
        $query->whereHas('category', function ($q) use ($request) {
            $q->where('name', $request->query('category'));
        });
    }

    // Filter by date range or month/year
    if ($request->filled('startDate') && $request->filled('endDate')) {
        $query->whereBetween('date', [$request->query('startDate'), $request->query('endDate')]);
    } elseif ($request->filled('month') || $request->filled('year')) {
        $month = $request->query('month', null);
        $year = $request->query('year', date('Y'));

        if ($month) {
            $startOfMonth = date("Y-m-d", strtotime("$year-$month-01"));
            $endOfMonth = date("Y-m-t", strtotime("$year-$month-01"));
            $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
        } else {
            $startOfYear = date("Y-01-01", strtotime("$year-01-01"));
            $endOfYear = date("Y-12-31", strtotime("$year-01-01"));
            $query->whereBetween('date', [$startOfYear, $endOfYear]);
        }
    }

    // Filter by amount range
    if ($request->filled('minAmount') && $request->filled('maxAmount')) {
        $query->whereBetween(DB::raw('ABS(amount)'), [$request->query('minAmount'), $request->query('maxAmount')]);
    }
    }
}
