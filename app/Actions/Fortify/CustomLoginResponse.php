<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request)
    {
        return redirect()->intended('/');
    }
}