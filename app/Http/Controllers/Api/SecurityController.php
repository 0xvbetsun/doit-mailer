<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use DB;
use File;
use Image;
use App\Models\User;
use App\Models\UserAvatar;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\User as UserResource;

/**
 * Class SecurityController
 * @package App\Http\Controllers\Api
 */
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
            if ($request->file('avatar')) {
                $file = $request->file('avatar');

                $avatarName = time() . '-' . $file->getClientOriginalName();
                $thumbnailName = 'thumbnail-' . $avatarName;

                $file->move(storage_path('uploads'), $avatarName);
                $ownerPath = "users/$user->id/";
                $savePath = storage_path('app/public/' . $ownerPath);
                File::makeDirectory($savePath);
                Image::make(storage_path('uploads/' . $avatarName))
                    ->resize(512, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->save($savePath . $avatarName, 100)
                ;
                $user->avatars()->create([
                    'name' => $avatarName,
                    'type' => UserAvatar::TYPE_MAIN,
                    'path' => $ownerPath
                ])
                ;

                Image::make(storage_path('uploads/' . $avatarName))
                    ->resize(150, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->save($savePath . $thumbnailName, 100)
                ;
                $user->avatars()->create([
                    'name' => $thumbnailName,
                    'type' => UserAvatar::TYPE_THUMBNAIL,
                    'path' => $ownerPath
                ])
                ;
            }
        });

        return response()->json(new UserResource($user), 201);
    }

}
