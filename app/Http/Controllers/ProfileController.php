<?php

namespace App\Http\Controllers;

use App\Models\ActiveCode;
use App\Notifications\ActiveCodeNotification;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

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
                    'two_factor_type' => 'sms'
                ]);
            } else {
                $code = ActiveCode::generateCode($request->user());
                $request->user()->notify(new ActiveCodeNotification($code));
                // flash session keep $data[phone] for one route after itself
                $request->session()->flash('phone', $data['phone']);
                return redirect(route('profile.get-phone-verify'));
            }
        } else if ($data['type'] == 'off') {
            $request->user()->update([
                'two_factor_type' => 'off',
            ]);
        }

        return back();
    }

    public function getPhoneVerify(Request $request)
    {
        if ($request->session()->has('phone')) {
            // keep phone data one route more
            $request->session()->reflash();
            return view('profile.phone-verify');
        }

        return view('profile.index');
    }

    public function postPhoneVerify(Request $request)
    {
        $data = $request->validate([
            'token' => 'required'
        ]);

        $status = ActiveCode::verifyCode($data['token'], $request->user());

        if ($status) {
            $request->user()->activeCodes()->delete();
            $request->user()->update([
                'two_factor_type' => 'sms',
                'phone_number' => $request->session()->get('phone')
            ]);
            Alert::success('Success', 'Your phone has been verified successfully!')->persistent(true);
            return redirect(route('profile.two-factor-auth'));
        } else {
            Alert::error('Error', 'Verification failed. Please try again.')->persistent(true);
            return redirect(route('profile.get-phone-verify'));
        }
    }
}
