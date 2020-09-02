<?php

namespace app\models\Services;

use app\models\LectureForm;
use yii\web\UploadedFile;

class FileHandler
{
    const CARD_UIDS_DELIMINER = '/\n|\r\n/';
    const PARTICIPANTS_DELIMINER = ';';

    public static function getPresencesCardUids(string $file_name): array
    {
        $file = UploadedFile::getInstanceByName($file_name);

        return null !== $file
            ? array_filter(array_unique(preg_split(static::CARD_UIDS_DELIMINER, file_get_contents($file->tempName))))
            : [];
    }

    public static function getParticipantIds(LectureForm $model, string $file_name): array
    {
        $file = UploadedFile::getInstance($model, $file_name);

        return null !== $file
            ? array_filter(rray_unique(explode(static::PARTICIPANTS_DELIMINER, file_get_contents($file->tempName))))
            : [];
    }
}