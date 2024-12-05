<?php

namespace App\Http\Controllers\Auth\Socials;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
            //         "name" => "aj Owusu",
            //         "username" => "bernie",
            //         "avatar" => "avatars/profile.png",
            //         "email" => "adjoa.owusu@gmail.com",
            //         "auth_type" => "google",
            //         "password" => "bernice1234",
            //         "token" => "sksguyugvyedyfxgtsuyhijzknmdbjhgsvxytguyij",
            //         "id" => 20
            //     ]));

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                $user->update([
                    'access_token' => $googleUser->token,
                    "token_type" => "Bearer",
                    'token_expiration' => now()->addSeconds($googleUser->expiresIn)->format('Y-m-d H:i:s'),
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    "username" => $googleUser->getNickname(),
                    "avatar" => $googleUser->getAvatar(),
                    'password' => bcrypt($googleUser->getName()),
                    'email' => $googleUser->getEmail(),
                    'auth_type' => $googleUser->auth_type,
                    'access_token' => $googleUser->token,
                    "token_type" => "Bearer",
                    'token_expiration' => now()->addSeconds($googleUser->expiresIn)->format('Y-m-d H:i:s'),
                ]);
            }

            dd($user);

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
