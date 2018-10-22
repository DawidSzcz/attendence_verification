<?php

namespace app\models;

use yii\base\Model;

class LecturerForm extends Model
{
    public $name;
    public $surname;

    public function rules()
    {
        return [
            [['name', 'surname'], 'required']
        ];
    }

    public function addLecturer()
    {
        \Yii::error($this->name . $this->surname);
        $lecturer = new Lecturer();

        $lecturer->name = $this->name;
        $lecturer->surname = $this->surname;

        $lecturer->save();
    }
}