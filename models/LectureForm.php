<?php

namespace app\models;

class LectureForm extends \yii\base\Model
{
    public $time;
    public $name;
    public $lecturer_name;
    public $classroom;

    public function rules()
    {
        return [
            [['name', 'time', 'lecturer_name', 'classroom'], 'required']
        ];
    }

    public function addLecture()
    {
        $lecture = new Lecture();

        list($name, $surname) = explode(' ', $this->lecturer_name);
        $lecture->time = $this->time;
        $lecture->name = $this->name;
        $lecture->lecturer_id = Lecturer::findOne(['name' => $name, 'surname' => $surname])->id;
        $lecture->classroom = $this->classroom;
        $lecture->save();
    }
}