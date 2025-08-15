<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public  function  index()
    {
        // Simulated dummy categories
        $categories = [
            (object)['id' => 1, 'name' => 'Food'],
            (object)['id' => 2, 'name' => 'Transport'],
            (object)['id' => 3, 'name' => 'Groceries'],
        ];

        // Simulated dummy expenses
        $expenses = [
            (object)[
                'id' => 1,
                'description' => 'Starbucks Coffee',
                'amount' => 5.25,
                'date' => '2025-08-10',
                'category_id' => 1,
                'category' => (object)['name' => 'Food'],
            ],
            (object)[
                'id' => 2,
                'description' => 'Uber ride',
                'amount' => 12.80,
                'date' => '2025-08-11',
                'category_id' => 2,
                'category' => (object)['name' => 'Transport'],
            ],
        ];


        return view('expenses.index', compact('categories', 'expenses'));
    }
}
