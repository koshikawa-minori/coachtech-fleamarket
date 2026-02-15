<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Http\Requests\TransactionMessageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        if ($transaction->buyer_user_id !== $user->id && $transaction->seller_user_id !== $user->id) {
            abort(403);
        }

        $sidebarTransactions = Transaction::whereIn('situation', [1,2])
            ->where(function ($query) use ($user) {
                    $query->where('buyer_user_id', $user->id)
                    ->orWhere('seller_user_id', $user->id);
            })
            ->with(['item'])
            ->withMax('transactionMessages', 'created_at')
            ->orderByRaw('transaction_messages_max_created_at IS NULL')
            ->orderByDesc('transaction_messages_max_created_at')
            ->orderByDesc('id')->get();

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

        $draftMessage = session("transaction_drafts.$transactionId");

        return view('transaction', compact('user','transaction', 'partnerUser', 'sidebarTransactions', 'draftMessage'));

    }

    public function store(TransactionMessageRequest $request, $transactionId)
    {
        $user = Auth::user();
        $storedPath = null;
        $transaction = Transaction::findOrFail($transactionId);

        if ($transaction->buyer_user_id !== $user->id && $transaction->seller_user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->store('items', 'public');
        }

        DB::transaction(function () use ($validated, $storedPath, $transaction) {
            $message = TransactionMessage::create([
                'transaction_id' => $transaction->id,
                'sender_id' => Auth::id(),
                'message' => $validated['message'],
                'image_path' => $storedPath,
            ]);

            return $message;
        });

        session()->forget("transaction_drafts.$transactionId");

        return redirect()->route('transaction.show', ['transactionId' => $transactionId]);

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

    public function draft(Request $request)
    {
        $user = Auth::user();
        $currentTransactionId = $request->input('current_transaction_id');

        $transaction = Transaction::findOrFail($currentTransactionId);
        if ($transaction->buyer_user_id !== $user->id && $transaction->seller_user_id !== $user->id) {
            abort(403);
        }

        $message = $request->input('message');
        $trimmedMessage = trim($message);
        if ($trimmedMessage === '')  {
            session()->forget("transaction_drafts.$currentTransactionId");
        }else{
            session(["transaction_drafts.$currentTransactionId" => $trimmedMessage]);
        }

        $destinationTransactionId = $request->input('destination_transaction_id');

        return redirect()->route('transaction.show', ['transactionId' => $destinationTransactionId]);
    }
}
