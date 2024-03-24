<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\TwoFactorAuthenticate;
use \Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    use TwoFactorAuthenticate;
    public function redirect()
    {
        // first you should add google provider to service.php
        // then call google driver to redirect user to google login page
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::query()
                ->where('email', $googleUser->email)
                ->first();
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => bcrypt(\Str::random(16))
                ]);
            }

            auth()->loginUsingId($user->id);

            return $this->loggedIn($request, $user) ?: redirect('/home');
        } catch (\Exception $error) {
            redirect('/login');
        }
    }
}
