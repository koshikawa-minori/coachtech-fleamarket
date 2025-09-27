<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Http\Requests\FortifyRegisterRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{


    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        /** @var FortifyRegisterRequest $registerRequest */
        $registerRequest = app(FortifyRegisterRequest::class);

        Validator::make(
            $input,
            $registerRequest->rules(),
            $registerRequest->messages()
        )->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
