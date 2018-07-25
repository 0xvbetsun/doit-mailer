<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use DB;
use App\Http\Resources\User as UserResource;

class SecurityController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $hasher = app()->make('hash');
        $user = (new User)
            ->where('email', '=', $request->input('email'))
            ->first()
        ;

        if (!$user) {
            return response()->json([
                'message' => sprintf('The user with email: "%s" doesn\'t exist!', $request->input('email'))
            ], 404);
        }

        if (!$hasher->check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'Password is incorrect!'
            ], 422);
        }

        return response()->json(new UserResource($user));
    }

    /**
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        DB::transaction(function () use ($request, &$user) {
            $hasher = app()->make('hash');
            $user = User::create([
                'email' => $request->input('email'),
                'password' => $hasher->make($request->input('password'))
            ]);
            if($request->filled('avatar')){
                dd('asd');
            }
        });

        return response()->json(new UserResource($user), 201);
    }
}