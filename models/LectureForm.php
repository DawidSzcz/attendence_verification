<?php

namespace app\models;

use app\models\Services\FileHandler;
use yii\base\Exception;
use yii\web\UploadedFile;

class LectureForm extends \yii\base\Model
{
    public $first_date;
    public $name;
    public $description;
    public $grain;
    public $last_date;
    public $once_date;
    public $participants;
    public $time;
    public $owner_id;

    const WEEK = 604800;
    const MONTH = 2419200;
    const GRAINS = ['monthly', 'weekly', 'once'];

    public function rules()
    {
        return [
            [['name', 'description', 'grain', 'time'], 'required'],
            ['name', 'string', 'length' => [4, 250]],
            ['description', 'string', 'length' => [4, 1000]],
            [['once_date', 'first_date', 'last_date'], 'date'],
            ['time', 'time']
        ];
    }

    public function addLecture(int $user_id)
    {
        $lecture = new Lecture();

        $lecture->name = $this->name;
        $lecture->description = $this->description;
        $lecture->owner = $user_id;
        $lecture->save();

        $album_nos = FileHandler::getParticipantIds($this, 'participants');
        $students = \Yii::$app->studentBase->retrieveStudentsByAlbumNos($album_nos);

        foreach ($students as $album_no => $student) {
            if ([] === $student) {
                \Yii::$app->session->addFlash('notice', sprintf("Unknown student with album number: %s", $album_no));
            } else {

                $participant = Participant::findOne(['external_ref' => $student['id']]);

                if (null === $participant) {
                    $participant = new Participant();
                    $participant->external_ref = $student['id'];
                    $participant->save();
                }

                $participation = new Participation();
                $participation->lecture_id = $lecture->getPrimaryKey();
                $participation->participant_id = $participant->getPrimaryKey();
                $participation->save();
            }
        }


        switch ($this->grain) {
            case 'once':
                $lecture_date = new LectureDate();
                $lecture_date->ts = $this->once_date . ' ' . $this->time;
                $lecture_date->lecture_id = $lecture->getPrimaryKey();

                $lecture_date->save();
                break;
            case 'weekly':
                $date = strtotime($this->first_date . ' ' . $this->time);
                $end = strtotime($this->last_date . ' ' . $this->time);
                while ($date <= $end) {
                    $lecture_date = new LectureDate();
                    $lecture_date->ts = date('Y-m-d H:i:s', $date);
                    $lecture_date->lecture_id = $lecture->getPrimaryKey();

                    $lecture_date->save();

                    $date += static::WEEK;
                }
                break;
            case 'monthly':
                $date = strtotime($this->first_date . ' ' . $this->time);
                $end = strtotime($this->last_date . ' ' . $this->time);
                while ($date <= $end) {
                    $lecture_date = new LectureDate();
                    $lecture_date->ts = date('Y-m-d H:i:s', $date);
                    $lecture_date->lecture_id = $lecture->getPrimaryKey();

                    $lecture_date->save();

                    $date += static::MONTH;
                }
                break;
            default:
                throw new Exception(sprintf('wrong grain in lecture form [%s]', $this->grain));
        }


    }
}