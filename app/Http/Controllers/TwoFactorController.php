<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Constants;

class TwoFactorController extends Controller
{
    function index() {
        $user = Auth::user();
        if($user->twofactor)
            return view("twofactor/show", compact('user'));
        else {
            $g2fa = app('pragmarx.google2fa');
            $key = $g2fa->generateSecretKey();
            $qrcode = $g2fa->getQRCodeInline(config('app.name'), $user->email, $key);
            return view('twofactor/enable', compact('user', 'key', 'qrcode'));
        }
    }

    function enable(Request $request) {
        $request->validate([
            'key'=>['required', 'string'],
            'code'=>['required', function($attr, $value, $fail) use ($request) {
                $g2fa = app('pragmarx.google2fa');
                
                if(!$g2fa->verifyKey($request->input('key'), $value)) {
                    $fail('Código inválido');
                }
            }]
        ]);

        $user = Auth::user();
        $user->twofactor = $request->input('key');
        $user->save();

        session(config("google2fa.session_var").".".Constants::SESSION_AUTH_PASSED, true);

        return redirect("/");
    }

    function disable() {
        $user = Auth::user();
        $user->twofactor = null;
        $user->save();

        return redirect('/');

    }
}
