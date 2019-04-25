<?php

namespace app\models;

class Participation extends \yii\db\ActiveRecord
{
    public function getLecture()
    {
        return $this->hasOne(Lecture::class, ['id' => 'lecture_id']);
    }


    public function getParticipant()
    {
        return $this->hasOne(Participant::class, ['id' => 'participant_id']);
    }
}