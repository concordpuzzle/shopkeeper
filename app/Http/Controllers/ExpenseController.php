<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('category')->orderBy('date', 'desc')->get();
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric',
            'scan_url' => 'nullable|url',
        ]);

        Expense::create($request->all());

        return redirect()->route('expenses.index')
            ->with('success', 'Expense added successfully');
    }
}
