<?php

namespace App\Http\Controllers\Auth\Socials;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function handleFacebookRedirect()
    {
        return Socialite::driver('facebook')->redirect();
    }
    public function handleFacebookCallback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->user();
            $user = User::where('email', $fbUser->getEmail())->where('auth_type', 'facebook')->first();
            if ($user) {
                $user->update([
                    'access_token' => $fbUser->token,
                    "token_type" => "Bearer",
                    'token_expiration' => now()->addSeconds($fbUser->expiresIn),
                ]);
            } else {
                $user = User::create([
                    'name' => $fbUser->name,
                    'email' => $fbUser->email,
                    'auth_type' => 'facebook',
                    'access_token' => $fbUser->token,
                    "token_type" => "Bearer",
                    'token_expiration' => now()->addSeconds($fbUser->expiresIn)->format('Y-m-d H:i:s'),
                ]);
            }
            Auth::login($user);
            return response()->json([
                'data' => [
                    'access_token' => $fbUser->token,
                    "token_type" => "Bearer",
                    "token_expiration" => now()->addSeconds($fbUser->expiresIn)->format('Y-m-d H:i:s'),
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
