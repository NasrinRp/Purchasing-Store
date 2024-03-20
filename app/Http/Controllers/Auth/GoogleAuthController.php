<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        // first you should add google provider to service.php
        // then call google driver to redirect user to google login page
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $existingUser = User::query()
                ->where('email', $googleUser->email)
                ->first();
            if ($existingUser) {
                auth()->loginUsingId($existingUser->id);
            } else {
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => bcrypt(\Str::random(16))
                ]);

                auth()->loginUsingId($newUser->id);
            }
            return redirect('/');
        } catch (\Exception $error) {
            return 'You have Error for login with google!';
        }
    }
}
