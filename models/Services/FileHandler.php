<?php

namespace app\models\Services;

use yii\web\UploadedFile;

class FileHandler
{
    const PARTICIPANS_DELIMINER = ';';

    public static function getParticipantIds(string $file_name): array
    {
        $file = UploadedFile::getInstanceByName($file_name);

        return null !== $file
            ? explode(static::PARTICIPANS_DELIMINER, $file->tempName)
            : [];
    }
}