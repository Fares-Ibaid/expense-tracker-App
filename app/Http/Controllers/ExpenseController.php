<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public  function  index()
    {
        // load categories and expenses from the database
        $categories = Category::all();
        $expenses = Expense::with('category')->get();

        return view('expenses.index', compact('categories', 'expenses'));
    }
}
