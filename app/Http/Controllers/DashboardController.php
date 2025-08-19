<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;

class DashboardController extends Controller
 {

    public function index()
    {
        // read expenses from the database
        $categories = Category::all();
        $expenses = Expense::with('category')->get();

        $total = $expenses->sum('amount');
        $count = $expenses->count();

        return view('dashboard.index', compact('expenses', 'total', 'count'));
    }
 }
