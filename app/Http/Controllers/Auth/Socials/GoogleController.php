<?php

namespace App\Http\Controllers\Auth\Socials;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function handleGoogleRedirect(){
        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleCallback(){
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('auth_id', $googleUser->id)->where('auth_type', 'google')->first();
            if(!$user){
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'auth_id' => $googleUser->id,
                    'auth_type' => 'google',
                ]);
            }
            Auth::login($user);
            return response()->json(['user' => $user]);

        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
