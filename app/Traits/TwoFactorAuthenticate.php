<?php

namespace App\Traits;

use App\Models\ActiveCode;
use App\Notifications\ActiveCodeNotification;
use Illuminate\Http\Request;

trait TwoFactorAuthenticate
{
    public function loggedIn(Request $request, $user): false|\Illuminate\Http\RedirectResponse
    {
        if ($user->isTwoFactorAuthEnable()) {
            auth()->logout();

            $request->session()->flash('auth', [
                'user_id' => $user->id,
                'using_sms' => false,
                'remember' => $request->has('remember')
            ]);

            if ($user->two_factor_type == 'sms') {
                $code = ActiveCode::generateCode($user);
                $request->user()->notify(new ActiveCodeNotification($code));

                $request->session()->push('auth.using_sms', true);
            }
            return redirect()->route('auth.token.get-token');
        }

        return false;
    }
}
