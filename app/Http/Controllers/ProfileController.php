<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;
use App\Http\Requests\ProfileRequest;
use App\Models\TransactionMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\select;

class ProfileController extends Controller
{
    // プロフィール画面表示
    public function show()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $profile = $user->profile;

        $page = request('page', 'sell');
        $totalUnreadCount = 0;
        if ($page === 'buy') {
            $transactions = collect();
            $items = $user->purchasedItems()->get();
        } elseif ($page === 'transaction') {
            $items = collect();
            $transactions = Transaction::whereIn('situation', [1,2])
            ->where(function ($query) use ($user) {
                    $query->where('buyer_user_id', $user->id)
                    ->orWhere('seller_user_id', $user->id);
            })
            ->with([
                'item',
                'transactionMessages' => function ($query) {
                    $query->select('id', 'transaction_id', 'sender_id', 'created_at');
                    },
                ])
            ->withMax('transactionMessages', 'created_at')
            ->orderByRaw('transaction_messages_max_created_at IS NULL')
            ->orderByDesc('transaction_messages_max_created_at')
            ->get();

            foreach ($transactions as $transaction) {
                if ($transaction->buyer_user_id === $user->id) {
                    $readAt = $transaction->buyer_read_at;
                } else {
                    $readAt = $transaction->seller_read_at;
                }

                $unreadCount = 0;
                foreach ($transaction->transactionMessages as $message) {
                    if ($message->sender_id !== $user->id) {
                        if ($readAt === null) {
                            $unreadCount++;
                        } elseif ($message->created_at > $readAt) {
                            $unreadCount++;
                        }
                    }
                }
                $transaction->unread_count = $unreadCount;
                $totalUnreadCount += $unreadCount;
            }

        } else {
            $transactions = collect();
            $items = $user->items()->get();
        }

        return view('mypage', compact('user', 'profile', 'items', 'transactions', 'totalUnreadCount'));

    }

    // プロフィール編集画面表示
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profile', compact('user', 'profile'));

    }

    // 更新処理等
    public function update(ProfileRequest $request)
    {
        // 認証ユーザー取得
        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();

        $isFirst = !$authenticatedUser->profile()->exists();

        // users.nameを更新
        $authenticatedUser->update([
            'name' => $request->input('name'),
        ]);

        // 画像アップロード
        $newImagePath = null;
        if ($request->hasFile('image_path')) {
            $oldPath = optional($authenticatedUser->profile)->image_path;
            if (filled($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $newImagePath = $request->file('image_path')->store('profile_images', 'public');
        }

        // プロフィール更新と作成
        $currentProfile = $authenticatedUser->profile;
        $imagePathToSave = null;

        if (!empty($newImagePath)) {
            $imagePathToSave = $newImagePath;
        } elseif (!empty($currentProfile) && !empty($currentProfile->image_path)) {
            $imagePathToSave = $currentProfile->image_path;
        } else {
            $imagePathToSave = null;
        }

        $profileData = [
            'user_id' => $authenticatedUser->id,
            'image_path'  => $imagePathToSave,
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ];

        $authenticatedUser->profile()->updateOrCreate(
            ['user_id' => $authenticatedUser->id],
            $profileData
        );

        if ($isFirst) {
            return redirect()->route('items.index');
        } else {
            return redirect()->route('mypage');
        }
    }
}
