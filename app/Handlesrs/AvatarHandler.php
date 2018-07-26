<?php
declare(strict_types=1);

namespace App\Handlers;

use File;
use Image;
use App\Models\User;
use App\Models\UserAvatar;
use Illuminate\Http\UploadedFile;

/**
 * Class AvatarHandler
 * @package App\Handlers
 */
class AvatarHandler
{
    private $file;

    private $user;

    /**
     * AvatarHandler constructor.
     * @param UploadedFile $file
     * @param User $user
     */
    public function __construct(UploadedFile $file, User $user)
    {
        $this->file = $file;
        $this->user = $user;
    }

    public function handle()
    {
        list($avatarName, $thumbnailName) = $this->createNamesForAvatars();
        list($ownerPath, $savePath) = $this->storeImageInSystem($avatarName);

        File::makeDirectory($savePath);

        $this->saveAvatarImages($avatarName, $savePath, $thumbnailName);
        $this->createUserAvatars($avatarName, $ownerPath, $thumbnailName);
    }

    /**
     * @return array
     */
    private function createNamesForAvatars(): array
    {
        $avatarName = time() . '-' . $this->file->getClientOriginalName();
        $thumbnailName = 'thumbnail-' . $avatarName;

        return [$avatarName, $thumbnailName];
    }

    /**
     * @param string $avatarName
     * @return array
     */
    private function storeImageInSystem(string $avatarName): array
    {
        $this->file->move(storage_path('uploads'), $avatarName);
        $id = $this->user->id;
        $ownerPath = "users/$id/";
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
     * @param string $avatarName
     * @param string $ownerPath
     * @param string $thumbnailName
     */
    private function createUserAvatars(string $avatarName, string $ownerPath, string $thumbnailName): void
    {
        $this->user->avatars()->create([
            'name' => $avatarName,
            'type' => UserAvatar::TYPE_MAIN,
            'path' => $ownerPath
        ])
        ;
        $this->user->thumbnails()->create([
            'name' => $thumbnailName,
            'type' => UserAvatar::TYPE_THUMBNAIL,
            'path' => $ownerPath
        ])
        ;
    }
}