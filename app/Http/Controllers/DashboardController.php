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

        // Filter by Date Range or Month/Year
   if ($request->filled('startDate') && $request->filled('endDate')) {
            $filtersApplied = true;
            $query->whereBetween('date', [$request->query('startDate'), $request->query('endDate')]);
         }elseif ($request->filled('month') || $request->filled('year')) {
                $filtersApplied = true;
                //dd($request->all());
                $month = $request->query('month', null); // Null if not provided
                $year = $request->query('year', date('Y')); // Default to current year if not provided

                if ($month) {
                    // If month is provided, filter for the given month and year
                    $startOfMonth = date("Y-m-d", strtotime("$year-$month-01"));
                    $endOfMonth = date("Y-m-t", strtotime("$year-$month-01"));
                    $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
                } else {
                    // If only year is provided, filter for the entire year
                    $startOfYear = date("Y-01-01", strtotime("$year-01-01"));
                    $endOfYear = date("Y-12-31", strtotime("$year-01-01"));
                    $query->whereBetween('date', [$startOfYear, $endOfYear]);
                }
        }
        // filter by amount
        if ($request->filled('minAmount') && $request->filled('maxAmount')) {
            $filtersApplied = true;
            $query->whereBetween(DB::raw('ABS(amount)'), [$request->query('minAmount'), $request->query('maxAmount')]);
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
