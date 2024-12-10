<?php

namespace App\Http\Controllers\Auth\Socials;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function handleGoogleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // $googleUser = json_decode(json_encode([
            //     "name" => "test20",
            //     "email" => "abcde@gmail.com",
            //     "nickname" => "nicky",
            //     "token" => "takenhere"
            // ]));

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                $user->update([
                    'access_token' => $googleUser->token,
                    "token_type" => "Bearer",
                    'token_expiration' => now()->addSeconds($googleUser->expiresIn)->format('Y-m-d H:i:s'),
                ]);
            } else {
                $user = User::create([
                    'name' =>  $googleUser->name,
                    "username" => $googleUser->nickname ?? "username",
                    'email' => $googleUser->email,
                    'auth_type' => [],
                    'password' => bcrypt(Str::random(10)),
                ]);
            }

            $user->addAuthType('google');

            // Auth::login($user);
            return response()->json([
                'data' => [
                    'access_token' => $googleUser->token,
                    "token_type" => "Bearer",
                    'token_expiration' => now()->addSeconds($googleUser->expiresIn)->format('Y-m-d H:i:s'),
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
