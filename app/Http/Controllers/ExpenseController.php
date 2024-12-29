<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('category');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        return Expense::create(array_merge($validated, ['user_id' => auth()->id()]));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $expense->update($validated);

        return $expense;
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }

    public function summary(Request $request)
    {
        $expenses = Expense::where('user_id', auth()->id())
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get();

        return $expenses;
    }

    public function show(Expense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $expense;
    }
}
