<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActiveCode;
use \App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TokenController extends Controller
{
    public function getToken(Request $request)
    {
        if (!$request->session()->has('auth')) {
            return redirect()->route('login');
        }

        $request->session()->reflash();
        return view('auth.token');
    }

    public function postToken(Request $request)
    {
        if (!$request->session()->has('auth')) {
            return redirect()->route('login');
        }

        $request->validate([
            'token' => 'required'
        ]);

        $user = User::query()->findOrFail($request->session()->get('auth.user_id'));

        $status = ActiveCode::verifyCode($request->token, $user);

        if ($status) {
            if (auth()->loginUsingId($user->id, $request->session()->get('auth.remember'))) {
                $user->activeCodes()->delete();
                Alert::success('Success', 'You login successfully!')->persistent(true);
                redirect()->route('home');
            }
        }

        Alert::error('Error', 'Your login failed. Please try again.')->persistent(true);
        return redirect()->route('login');
    }
}
