<?php


namespace app\models\Services;


use app\models\Lecture;
use app\models\LectureDate;

class RaportGenerator
{
    const DELIMITER = ',';
    const DATE_FORMAT = "Y-m-d";

    public static function generateRaport(Lecture $lecture, array $students, array $participants): string
    {
        $csv = 'ALBUM NUMBER,NAME,SURNAME';

        /** @var LectureDate $lecture_date */
        foreach ($lecture->lectureDates as $lecture_date) {
            $csv .= static::DELIMITER . $lecture_date->ts;
        }

        $csv .= PHP_EOL;

        foreach ($participants as $participant) {
            $student = $students[$participant->external_ref];

            $csv .= $student['album_no'] . static::DELIMITER . $student['name'] . static::DELIMITER . $student['surname'];

            foreach ($lecture->lectureDates as $lecture_date) {
                $csv .= static::DELIMITER . ($participant->isPresent($lecture_date->id) ? '1' : '0');
            }
            $csv .= PHP_EOL;
        }

        return $csv;
    }

    private static function getPresences()
    {

    }
}