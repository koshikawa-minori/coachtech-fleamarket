<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionMessageRequest;
use App\Http\Requests\UpdateTransactionMessageRequest;
use App\Mail\TransactionCompletedMail;
use App\Models\Evaluation;
use App\Models\Transaction;
use App\Models\TransactionMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function show(Request $request, $transactionId)
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

        $latestTransactionMessageId = $transaction->transactionMessages->max('id');

        $draftMessage = session("transaction_drafts.$transactionId");

        $editMessageId = $request->query('edit_message_id');

        $isBuyer = $user->id === $transaction->buyer_user_id;
        $isSeller = $user->id === $transaction->seller_user_id;

        $hasUserReviewed = Evaluation::where('transaction_id', $transaction->id)
            ->where('evaluator_id', $user->id)
            ->exists();

        $canBuyerReview = $isBuyer
            && $transaction->situation === 1
            && !$hasUserReviewed;

        $canSellerReview = $isSeller
            && $transaction->situation === 2
            && !$hasUserReviewed;


        $modalQuery = $request->query('modal');

        if ($canSellerReview && $modalQuery === null) {
            return redirect()->route('transaction.show', ['transactionId' => $transactionId, 'modal' => 'review'])->withFragment('review-modal');
        }

        return view('transaction', compact('user','transaction', 'partnerUser', 'sidebarTransactions', 'draftMessage', 'editMessageId', 'latestTransactionMessageId', 'canBuyerReview', 'canSellerReview', 'modalQuery'));
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
            $storedPath = $request->file('image')->store('chat_images', 'public');
        }

        DB::transaction(function () use ($validated, $storedPath, $transaction) {
            TransactionMessage::create([
                'transaction_id' => $transaction->id,
                'sender_id' => Auth::id(),
                'message' => $validated['message'],
                'image_path' => $storedPath,
            ]);
        });

        session()->forget("transaction_drafts.$transactionId");

        return redirect()->route('transaction.show', ['transactionId' => $transactionId]);
    }

    public function edit($messageId)
    {
        $user = Auth::user();
        $message = TransactionMessage::findOrFail($messageId);
        $transactionId = $message->transaction_id;

        if ($message->sender_id !== $user->id) {
            abort(403);
        }

        $latestTransactionMessageId = TransactionMessage::where('transaction_id', $transactionId)->max('id');

        if ($message->id !== $latestTransactionMessageId) {
            abort(403);
        }

        return redirect()->route('transaction.show', [
            'transactionId' => $transactionId,
            'edit_message_id' => $messageId,
        ]);
    }


    public function update(UpdateTransactionMessageRequest  $request, $messageId)
    {
        $user = Auth::user();
        $message = TransactionMessage::findOrFail($messageId);
        $transactionId = $message->transaction_id;

        if ($message->sender_id !== $user->id) {
            abort(403);
        }

        $latestTransactionMessageId = TransactionMessage::where('transaction_id', $transactionId)->max('id');

        if ($message->id !== $latestTransactionMessageId) {
            abort(403);
        }

        $validated = $request->validated();

        $message->update([
            'message' => $validated['edit_message'],
        ]);

        return redirect()->route('transaction.show', ['transactionId' => $transactionId]);
    }

    public function destroy($messageId)
    {
        $user = Auth::user();
        $message = TransactionMessage::findOrFail($messageId);
        $transactionId = $message->transaction_id;

        if ($message->sender_id !== $user->id) {
            abort(403);
        }

        $latestTransactionMessageId = TransactionMessage::where('transaction_id', $transactionId)->max('id');

        if ($message->id !== $latestTransactionMessageId) {
            abort(403);
        }

        $message->delete();

        return redirect()->route('transaction.show', ['transactionId' => $transactionId]);
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

    public function storeReview(Request $request, $transactionId)
    {
        $user = Auth::user();
        $transaction = Transaction::findOrFail($transactionId);

        if ($transaction->buyer_user_id === $user->id) {
            if ($transaction->situation !== 1) {
                abort(403);
            }
            $evaluatorUserId = $user->id;
            $evaluatedUserId = $transaction->seller_user_id;
            $nextSituation = 2;

        } elseif ($transaction->seller_user_id === $user->id) {
            if ($transaction->situation !== 2) {
                abort(403);
            }
            $evaluatorUserId = $user->id;
            $evaluatedUserId = $transaction->buyer_user_id;
            $nextSituation = 3;
        } else {
            abort(403);
        }

        $hasUserReviewed = Evaluation::where('transaction_id', $transaction->id)
            ->where('evaluator_id', $user->id)
            ->exists();

        if  ($hasUserReviewed) {
            return redirect()->route('items.index', ['tab' => 'mylist']);
        }

        $validated = $request->validate([
            'score' => ['required', 'integer', 'between:1,5']
        ]);

        DB::transaction(function () use ($validated, $transaction, $evaluatorUserId,$evaluatedUserId,$nextSituation) {
            Evaluation::create([
                'transaction_id' => $transaction->id,
                'evaluator_id' => $evaluatorUserId,
                'evaluated_id' => $evaluatedUserId,
                'score' => $validated['score'],
            ]);

            $transaction->update(['situation' => $nextSituation]);
        });

        if ($nextSituation === 2) {
            $transaction->loadMissing(['item', 'seller']);

            Mail::to($transaction->seller->email)
                ->send(new TransactionCompletedMail($transaction));
        }

        return redirect()->route('items.index', ['tab' => 'mylist']);
    }
}
