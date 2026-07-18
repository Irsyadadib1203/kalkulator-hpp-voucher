<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer'],
            'type' => ['required', 'in:BUY,SELL'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $category = Category::where('id', $validated['category_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (! $category) {
            return response()->json(['message' => 'Kategori voucher tidak ditemukan.'], 422);
        }

        if ($validated['type'] === 'SELL') {
            $bought = Transaction::where('user_id', Auth::id())
                ->where('category_id', $category->id)
                ->where('type', 'BUY')->sum('quantity');
            $sold = Transaction::where('user_id', Auth::id())
                ->where('category_id', $category->id)
                ->where('type', 'SELL')->sum('quantity');
            $currentStock = $bought - $sold;

            if ($validated['quantity'] > $currentStock) {
                return response()->json([
                    'message' => "Gagal: Jumlah jual ({$validated['quantity']}) melebihi sisa stok voucher yang dimiliki ({$currentStock} lembar).",
                ], 422);
            }
        }

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $category->id,
            'type' => $validated['type'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'total' => $validated['price'] * $validated['quantity'],
            'transaction_date' => now(),
        ]);

        return response()->json([
            'id' => (string) $transaction->id,
            'type' => $transaction->type,
            'voucherCode' => $category->name,
            'categoryId' => $category->id,
            'price' => (float) $transaction->price,
            'quantity' => (int) $transaction->quantity,
            'total' => (float) $transaction->total,
            'date' => $transaction->transaction_date->toISOString(),
        ], 201);
    }

    public function destroy(Transaction $transaction)
    {
        abort_unless($transaction->user_id === Auth::id(), 403);

        $transaction->delete();

        return response()->json(['message' => 'deleted']);
    }

    public function clear()
    {
        Transaction::where('user_id', Auth::id())->delete();

        return response()->json(['message' => 'cleared']);
    }
}
