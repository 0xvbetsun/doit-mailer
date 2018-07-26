<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use DB;
use File;
use Image;
use App\Models\User;
use App\Models\UserAvatar;
use Illuminate\Http\UploadedFile;
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

                list($avatarName, $thumbnailName) = $this->createNamesForAvatars($file);
                list($ownerPath, $savePath) = $this->storeImageInSystem($file, $avatarName, $user);

                File::makeDirectory($savePath);

                $this->saveAvatarImages($avatarName, $savePath, $thumbnailName);
                $this->createUserAvatars($user, $avatarName, $ownerPath, $thumbnailName);
            }
        });

        return response()->json(new UserResource($user), 201);
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    private function createNamesForAvatars(UploadedFile $file): array
    {
        $avatarName = time() . '-' . $file->getClientOriginalName();
        $thumbnailName = 'thumbnail-' . $avatarName;

        return [$avatarName, $thumbnailName];
    }

    /**
     * @param UploadedFile $file
     * @param string $avatarName
     * @param User $user
     * @return array
     */
    private function storeImageInSystem(UploadedFile $file, string $avatarName, User $user): array
    {
        $file->move(storage_path('uploads'), $avatarName);
        $ownerPath = "users/$user->id/";
        $savePath = storage_path('app/public/' . $ownerPath);

        return [$ownerPath, $savePath];
    }

    /**
     * @param string $avatarName
     * @param string $savePath
     * @param string $thumbnailName
     */
    private function saveAvatarImages(string $avatarName, string $savePath, string $thumbnailName): void
    {
        Image::make(storage_path('uploads/' . $avatarName))
            ->resize(512, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save($savePath . $avatarName, 100)
        ;
        Image::make(storage_path('uploads/' . $avatarName))
            ->resize(150, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save($savePath . $thumbnailName, 100)
        ;
    }

    /**
     * @param User $user
     * @param string $avatarName
     * @param string $ownerPath
     * @param string $thumbnailName
     */
    private function createUserAvatars(User $user, string $avatarName, string $ownerPath, string $thumbnailName): void
    {
        $user->avatars()->create([
            'name' => $avatarName,
            'type' => UserAvatar::TYPE_MAIN,
            'path' => $ownerPath
        ])
        ;
        $user->avatars()->create([
            'name' => $thumbnailName,
            'type' => UserAvatar::TYPE_THUMBNAIL,
            'path' => $ownerPath
        ])
        ;
    }
}
