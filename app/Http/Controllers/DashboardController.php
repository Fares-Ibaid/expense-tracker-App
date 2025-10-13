<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
 {

    public function index(Request $request)
    {
        // Base query
        $query = Expense::with('category');

        // Check if filters are applied
        $filtersApplied = false;
        // Filter by Category
           if ($request->filled('category')) {
               $filtersApplied = true;
               $query->whereHas('category', function ($q) use ($request) {
                   $q->where('name', $request->query('category'));
                  // $q->where('name', 'LIKE', $request->query('category'));
                //   $q->where('name', $request->query('category', ''));
                //   $q->whereRaw('LOWER(name) = ?', [strtolower($request->query('category'))]);
               });
               // Dump the query
              // dd($query->toSql(), $query->getBindings());
           }

        // Filter by Date Range
        if($request->has('startDate') && $request->has('endDate')) {
            $filtersApplied = true;
            $query->whereBetween('date', [$request->query('startDate'), $request->query('endDate')]);
        }

        // filter by amount
        if($request->has('min_amount') && $request->has('max_amount')) {
            $filtersApplied = true;
            $query->whereBetween('amount', [$request->query('min_amount'), $request->query('max_amount')]);
        }

        // pagination
        $perPage  = $request->query('per_page', 5); // this will be changed dynamically from the frontend
        $expenses = $query->paginate($perPage);


        $total = $expenses->total();
        $count = $expenses->count();

        return response()->json([
            'expenses' => $expenses,
            'total' => $total,
            'count' => $count,
        ]);

    }
 }
