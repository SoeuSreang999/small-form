<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser     = Socialite::driver('google')->user();
        $user = User::updateOrCreate(
            [
                'email'    => $googleUser->getEmail()
            ],
            [
                'username' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                'profile'  => $googleUser->getAvatar(),
                'password' => '12345678',
            ]
        );
        Auth::login($user, remember: true);
        return redirect()->intended('/');
    }
}
