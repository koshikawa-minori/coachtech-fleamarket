<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function show($transactionId)
    {
        return view('transaction');
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
