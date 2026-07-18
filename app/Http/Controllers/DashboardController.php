<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $categories = $user->categories()->orderBy('name')->get(['id', 'name']);

        $transactions = $user->transactions()
            ->with('category:id,name')
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => (string) $t->id,
                    'type' => $t->type,
                    'voucherCode' => $t->category->name,
                    'categoryId' => $t->category_id,
                    'price' => (float) $t->price,
                    'quantity' => (int) $t->quantity,
                    'total' => (float) $t->total,
                    'date' => $t->transaction_date->toISOString(),
                ];
            })
            ->values();

        return view('dashboard', [
            'initialCategories' => $categories->pluck('name')->values(),
            'initialCategoryIds' => $categories->pluck('id', 'name'),
            'initialTransactions' => $transactions,
        ]);
    }
}
