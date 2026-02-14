<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function show($transactionId)
    {
        $user = Auth::user();
        $transaction = Transaction::with([
            'item',
            'buyer.profile',
            'seller.profile',
            'transactionMessages'  => function ($query) {
                $query->with('sender.profile')->orderBy('created_at');
            },
        ])->findOrFail($transactionId);

        $sidebarTransactions = Transaction::whereIn('situation', [1,2])
            ->where(function ($query) use ($user) {
                    $query->where('buyer_user_id', $user->id)
                    ->orWhere('seller_user_id', $user->id);
            })
            ->with([
                'item',
            ])->orderByDesc('updated_at')->get();

        if ($transaction->buyer_user_id !== $user->id && $transaction->seller_user_id !== $user->id) {
            abort(403);
        }

        if ($user->id === $transaction->buyer_user_id) {
            $transaction->buyer_read_at = now();
        } else {
            $transaction->seller_read_at = now();
        }

        $transaction->save();

        if ($user->id === $transaction->buyer_user_id) {
            $partnerUser = $transaction->seller;
        } else {
            $partnerUser = $transaction->buyer;
        }

        return view('transaction', compact('user','transaction', 'partnerUser', 'sidebarTransactions'));

    }

    public function store(Request $request, $transactionId)
    {

    }

    public function update(Request $request, $messageId)
    {

    }

    public function destroy($messageId)
    {

    }

    public function markAsRead($transactionId)
    {

    }

    public function storeReview(Request $request, $transactionId)
    {

    }
}
