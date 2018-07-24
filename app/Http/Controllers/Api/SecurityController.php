<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SecurityController extends Controller
{
    public function login(Request $request)
    {
        $hasher = app()->make('hash');
        $user = (new User)->where('email', '=', $request->input('email'))
            ->first();
//        dd($user);

        if (!$user) {
            return response()->json([
                'message' => sprintf('The user with email: "%s" doesn\'t exist!', $request->input('email'))
            ], 404);
        }

        if(!$hasher->check($request->input('password'), $user->password)){
            return response()->json([
                'message' => 'Password is incorrect!'
            ], 422);
        }

        return response()->json(['data' => $user]);
    }
}
