<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request)
    {
        return redirect()->intended(route('items.index'));

    }
}