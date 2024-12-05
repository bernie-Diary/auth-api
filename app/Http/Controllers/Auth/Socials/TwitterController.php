<?php

namespace App\Http\Controllers\Auth\Socials;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class TwitterController extends Controller
{
    public function handleTwitterRedirect()
    {
        return Socialite::driver('twitter')->redirect();
    }
    public function handleTwitterCallback()
    {
        try {
            $xUser = Socialite::driver('twitter')->user();
            $user = User::where('email', $xUser->getEmail())->where('auth_type', 'twitter')->first();
            if ($user) {
                $user->update([
                    'access_token' => $xUser->token,
                    "token_type" => "Bearer",
                    'token_expiration' => now()->addSeconds($xUser->expiresIn),
                ]);
            } else {
                $user = User::create([
                    'name' => $xUser->name,
                    'email' => $xUser->email,
                    'auth_type' => 'twitter',
                    'access_token' => $xUser->token,
                    "token_type" => "Bearer",
                    'token_expiration' => now()->addSeconds($xUser->expiresIn)->format('Y-m-d H:i:s'),
                ]);
            }
            Auth::login($user);
            return response()->json([
                'data' => [
                    'access_token' => $xUser->token,
                    "token_type" => "Bearer",
                    "token_expiration" => now()->addSeconds($xUser->expiresIn)->format('Y-m-d H:i:s'),
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
