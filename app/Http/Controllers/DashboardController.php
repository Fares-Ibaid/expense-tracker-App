<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;

class DashboardController extends Controller
 {

    public function index(Request $request)
    {

        $perPage  = $request->query('per_page', 5); // this will be changed dynamically from the frontend
        $expenses = Expense::with('category')->paginate($perPage); // from laravel gives you Metadata

        $total = $expenses->total();
        $count = $expenses->count();

        return response()->json([
            'expenses' => $expenses,
            'total' => $total,
            'count' => $count,
        ]);

    }
 }
