<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use DB;
use App\Models\User;
use App\Handlers\AvatarHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\User as UserResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class SecurityController
 * @package App\Http\Controllers\Api
 */
class SecurityController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ModelNotFoundException
     * @throws ValidationException
     */
    public function login(LoginRequest $request)
    {
        $hasher = app()->make('hash');
        $user = (new User)
            ->where('email', '=', $request->input('email'))
            ->first()
        ;

        if (!$user) {
            throw new ModelNotFoundException(sprintf('The user with email: "%s" doesn\'t exist!', $request->input('email')));
        }

        if (!$hasher->check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Password is incorrect!'],
            ]);
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

            if ($request->file('avatar')) {
                $file = $request->file('avatar');

                $avatarHandler = app()->make(AvatarHandler::class, compact('file', 'user'));
                $avatarHandler->handle();
            }
        });

        return response()->json(new UserResource($user), 201);
    }
}
