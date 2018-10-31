<?php

namespace app\models;

class LectureForm extends \yii\base\Model
{
    public $date_time;
    public $name;
    public $lecturer_id;
    public $classroom;

    public function rules()
    {
        return [
            [['name', 'date_time', 'lecturer_id', 'classroom'], 'required']
        ];
    }

    public function addLecture()
    {
        $lecture = new Lecture();

        $lecture->time = $this->date_time;
        $lecture->name = $this->name;
        $lecture->lecturer_id = $this->lecturer_id;
        $lecture->classroom = $this->classroom;
        $lecture->save();
    }
}