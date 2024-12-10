<?php

namespace App\Http\Controllers\Auth\Socials;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

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
            $user = User::where('email', $fbUser->email)->first();
            if ($user) {
                $user->update([
                    'access_token' => $fbUser->token,
                    "token_type" => "Bearer",
                ]);
            } else {
                $user = User::create([
                    'name' =>  $fbUser->name,
                    "username" => $fbUser->nickname ?? "username",
                    'email' => $fbUser->email,
                    'auth_type' => [],
                    'password' => bcrypt(str::random(10)),
                ]);
            }
            // Auth::login($user);
            $user->addAuthType('facebook');
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
