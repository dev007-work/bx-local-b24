<?php

namespace B24\Project\Integration\Intranet\Employee;

use Bitrix\Intranet\Component\UserProfile;
use Bitrix\Intranet\UserTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Context\Culture;
use CComponentEngine;
use CUser;

class Service
{
    private Culture $culture;

    public function __construct()
    {
        $this->culture = Context::getCurrent()->getCulture();
    }

    public function getByIds(int ...$ids): array
    {
        $result = UserTable::query()
            ->setSelect([
                'ID',
                'PERSONAL_PHOTO',
                'NAME',
                'LAST_NAME',
                'SECOND_NAME',
                'LOGIN',
                'WORK_POSITION',
            ])
            ->where('USER_TYPE_IS_EMPLOYEE', true)
            ->where('ACTIVE', '=', 'Y')
            ->whereIn('ID', $ids)
            ->exec();

        $users = [];
        while ($user = $result->fetch()) {
            $userId = $user['ID'];
            $user = array_merge($user, [
                'PERSONAL_PHOTO_PATH' => $this->getPersonalPhotoPath($user['PERSONAL_PHOTO'] ?? 0),
                'PROFILE_URL'         => $this->getProfileUrl($userId),
                'FORMATTED_NAME'      => $this->formatName($user),
            ]);

            $users[$userId] = $user;
        }

        return $users;
    }

    public function getPersonalPhotoPath(int $photoId, ?int $size = null): ?string
    {
        return UserProfile::getUserPhoto($photoId, $size) ?: null;
    }

    public function getProfileUrl(int $userId): string
    {
        return CComponentEngine::makePathFromTemplate($this->getProfileUrlTemplate(), ['USER_ID' => $userId]);
    }

    public function getProfileUrlTemplate(): string
    {
        return Option::get('intranet', 'path_user') ?: '/company/personal/user/#USER_ID#/';
    }

    public function formatName(array $userInfo): string
    {
        return CUser::FormatName($this->culture->getNameFormat(), $userInfo);
    }
}