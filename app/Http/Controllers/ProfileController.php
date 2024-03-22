<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function twoFactorAuth()
    {
        return view('profile.two-factor-auth');
    }

    public function mangeTwoFactoryAuth(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:sms,off',
            'phone' => 'required_unless:type,off' // meaning: the phone field is required unless type value be off.
        ]);

        if ($data['type'] == 'sms') {
            if ($request->user()->phone_number == $data['phone']) {
                $request->user()->update([
                   'two_factor_auth' => 'sms'
                ]);
            } else {
                // create a unique code
                // send it to user
                return redirect(route('profile.get-phone-verify'));
            }
        } else if ($data['type'] == 'off') {
            $request->user()->update([
                'two_factor_auth' => 'off',
            ]);
        }

        return back();
    }

    public function getPhoneVerify()
    {
        return view('profile.phone-verify');
    }

    public function postPhoneVerify(Request $request)
    {
        $data = $request->validate([
            'token' => 'required'
        ]);
        return $request->token;
    }
}
