<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::query()
            ->with(['task', 'taskStep', 'actor'])
            ->latest()
            ->get();

        return Inertia::render('transaction/Index', [
            'transactions' => TransactionResource::collection($transactions),
        ]);
    }
}
