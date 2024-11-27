<?php

namespace App\Http\Controllers\Auth\Socials;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class TwitterController extends Controller
{
    public function handleTwitterRedirect(){
        return Socialite::driver('twitter')->redirect();
    }
    public function handleTwitterCallback(){
        try {
            $xUser = Socialite::driver('twitter')->user();
            $user = User::where('auth_id', $xUser->id)->where('auth_type', 'twitter')->first();
            if(!$user){
                $user = User::create([
                    'name' => $xUser->name,
                    'email' => $xUser->email,
                    'auth_id' => $xUser->id,
                    'auth_type' => 'twitter',
                ]);
            }
            Auth::login($user);
            return response()->json(['user' => $user]);

        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
