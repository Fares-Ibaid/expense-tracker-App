<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
 {

    public function index()
    {
        $expenses = collect([
            (object)[
                'description' => 'Starbucks',
                'category' => (object)['name' => 'Food'],
                'amount' => 5.25,
                'date' => '2025-08-10',
            ],
            (object)[
                'description' => 'Uber',
                'category' => (object)['name' => 'Transport'],
                'amount' => 12.80,
                'date' => '2025-08-11',
            ],
        ]);

        $total = $expenses->sum('amount');
        $count = $expenses->count();

        return view('dashboard.index', compact('expenses', 'total', 'count'));
    }
 }
