<?php

namespace app\models;

use yii\base\Exception;

class LectureForm extends \yii\base\Model
{
    public $first_date;
    public $name;
    public $description;
    public $grain;
    public $last_date;
    public $once_date;
    public $time;

    const WEEK = 604800;
    const MONTH = 2419200;
    const GRAINS = ['monthly', 'weekly', 'once'];

    public function rules()
    {
        return [
            [['name', 'description', 'grain', 'time'], 'required'],
            [['once_date', 'first_date', 'last_date'], 'date']
        ];
    }

    public function addLecture()
    {
        $lecture = new Lecture();

        $lecture->name = $this->name;
        $lecture->description = $this->description;
        $lecture->save();

        switch ($this->grain) {
            case 'once':
                $lecture_date = new LectureDate();
                $lecture_date->ts = $this->once_date . ' ' .$this->time;
                $lecture_date->lecture_id = $lecture->getPrimaryKey();

                $lecture_date->save();
                break;
            case 'weekly':
                $date = strtotime($this->first_date . ' ' .$this->time);
                $end = strtotime($this->last_date . ' ' .$this->time);
                while ($date <= $end) {
                    $lecture_date = new LectureDate();
                    $lecture_date->ts = date('Y-m-d H:i:s',$date);
                    $lecture_date->lecture_id = $lecture->getPrimaryKey();

                    $lecture_date->save();

                    $date += static::WEEK;
                }
                break;
            case 'monthly':
                $date = strtotime($this->first_date . ' ' .$this->time);
                $end = strtotime($this->last_date . ' ' .$this->time);
                while ($date <= $end) {
                    $lecture_date = new LectureDate();
                    $lecture_date->ts = date('Y-m-d H:i:s',$date);
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