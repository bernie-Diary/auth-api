<?php

namespace App\Http\Controllers\Auth\Socials;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;


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
            $user = User::where('email', $xUser->email)->first();

            if ($user) {
                $user->update([
                    'access_token' => $xUser->token,
                    "token_type" => "Bearer",
                ]);
            } else {
                $user = User::create([
                    'name' =>  $xUser->name,
                    "username" => $xUser->nickname ?? "username",
                    'email' => $xUser->email,
                    'auth_type' => [],
                    'password' => bcrypt(str::random(10)),
                ]);
            }
            $user->addAuthType('twitter');

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
