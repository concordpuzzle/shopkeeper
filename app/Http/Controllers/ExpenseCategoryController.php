<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories'
        ]);

        ExpenseCategory::create($request->all());

        return redirect()->route('expenses.index')
            ->with('success', 'Category added successfully');
    }
}
