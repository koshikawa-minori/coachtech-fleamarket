<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Models\Evaluation;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ユーザー３名
        $seller = User::where('email', 'seller@example.com')->firstOrFail();
        $seller2 = User::where('email', 'seller2@example.com')->firstOrFail();
        $buyer = User::where('email', 'test@example.com')->firstOrFail();

        // 取引対象商品
        $item = Item::where('name', 'HDD')->where('seller_user_id', $seller->id)->firstOrFail();
        $item2 = Item::where('name', 'ノートPC')->where('seller_user_id', $seller->id)->firstOrFail();
        $item3 = Item::where('name', 'コーヒーミル')->where('seller_user_id', $seller2->id)->firstOrFail();
        $item4 = Item::where('name', 'タンブラー')->where('seller_user_id', $seller2->id)->firstOrFail();

        // 購入済み処理等
        Item::whereIn('id', [$item->id, $item2->id, $item3->id, $item4->id])->update(['is_sold' => true]);

        $orderBase = [
            'buyer_user_id' => $buyer->id,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
            'building' => null,
            'payment_method' => 2,
        ];

        Order::create($orderBase + ['item_id' => $item->id]);
        Order::create($orderBase + ['item_id' => $item2->id]);
        Order::create($orderBase + ['item_id' => $item3->id]);
        Order::create($orderBase + ['item_id' => $item4->id]);

        // 取引内容
        $transactionBase = [
            'buyer_user_id' => $buyer->id,
            'buyer_read_at' => null,
            'seller_read_at' => null,
        ];

        $transaction = Transaction::create($transactionBase + [
            'item_id' => $item->id,
            'seller_user_id' => $seller->id,
            'situation' => 1,
            ]);

        $transaction2 = Transaction::create($transactionBase + [
            'item_id' => $item2->id,
            'seller_user_id' => $seller->id,
            'situation' => 3,
            'buyer_read_at' => now(),
            'seller_read_at' => now(),
            ]);

        $transaction3 = Transaction::create($transactionBase + [
            'item_id' => $item3->id,
            'seller_user_id' => $seller2->id,
            'situation' => 2,
            'buyer_read_at' => now(),
            'seller_read_at' => null,
            ]);

        $transaction4 = Transaction::create($transactionBase + [
            'item_id' => $item4->id,
            'seller_user_id' => $seller2->id,
            'situation' => 1,
            'buyer_read_at' => null,
            'seller_read_at' => null,
        ]);


        // 取引チャット
        $buyerMessageBase = [
            'message' => '購入いたしました。よろしくお願いします。',
            'image_path' => null,
        ];

        $sellerMessageBase = [
            'message' => 'お買い上げいただきありがとうございます。これから発送の準備をさせていただきます。',
            'image_path' => null,
        ];

        // 取引１(HDD)
        $now = Carbon::now();
        $transactionBaseTime = $now->copy()->subDays(1);
        TransactionMessage::create(
            $buyerMessageBase + [
                'transaction_id' => $transaction->id,
                'sender_id' =>  $buyer->id,
                'created_at' => $transactionBaseTime,
                'updated_at' => $transactionBaseTime,
            ]
        );

        TransactionMessage::create(
            $sellerMessageBase + [
                'transaction_id' => $transaction->id,
                'sender_id' =>  $seller->id,
                'created_at' => $transactionBaseTime->copy()->addMinute(),
                'updated_at' => $transactionBaseTime->copy()->addMinute(),
            ]
        );

        // 取引２(ノートPC)
        $transaction2BaseTime = $now->copy()->subDays(2);
        TransactionMessage::create(
            $buyerMessageBase + [
                'transaction_id' => $transaction2->id,
                'sender_id' =>  $buyer->id,
                'created_at' => $transaction2BaseTime,
                'updated_at' => $transaction2BaseTime,
            ]
        );

        TransactionMessage::create(
            $sellerMessageBase + [
                'transaction_id' => $transaction2->id,
                'sender_id' =>  $seller->id,
                'created_at' => $transaction2BaseTime->copy()->addMinute(),
                'updated_at' => $transaction2BaseTime->copy()->addMinute(),
            ]
        );

        //　取引３(コーヒーミル)
        $transaction3BaseTime = $now->copy();
        TransactionMessage::create(
            $buyerMessageBase + [
                'transaction_id' => $transaction3->id,
                'sender_id' =>  $buyer->id,
                'created_at' => $transaction3BaseTime,
                'updated_at' => $transaction3BaseTime,
            ]
        );

        TransactionMessage::create(
            $sellerMessageBase + [
                'transaction_id' => $transaction3->id,
                'sender_id' =>  $seller2->id,
                'created_at' => $transaction3BaseTime->copy()->addMinute(),
                'updated_at' => $transaction3BaseTime->copy()->addMinute(),
            ]
        );

        // 評価
        // 取引２(ノートPC)
        Evaluation::create([
            'transaction_id' => $transaction2->id,
            'evaluator_id' =>  $buyer->id,
            'evaluated_id' =>  $seller->id,
            'score' =>  3,
        ]);

        Evaluation::create([
            'transaction_id' => $transaction2->id,
            'evaluator_id' =>  $seller->id,
            'evaluated_id' =>  $buyer->id,
            'score' =>  4,
        ]);

        // 取引３(コーヒーミル)
        Evaluation::create([
            'transaction_id' => $transaction3->id,
            'evaluator_id' =>  $buyer->id,
            'evaluated_id' =>  $seller2->id,
            'score' =>  3,
        ]);
    }
}
