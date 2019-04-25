<?php
/**
 * Created by IntelliJ IDEA.
 * User: kazik
 * Date: 13.08.17
 * Time: 14:09
 */

namespace app\models;

class Lecture extends \yii\db\ActiveRecord
{
    public function getLectureDates()
    {
        return $this->hasMany(LectureDate::class, ['lecture_id' => 'id'])->orderBy('ts');
    }

    public function getParticipants()
    {
        return $this->hasMany(Participant::class, ['id' => 'participant_id'])->viaTable('participation', ['lecture_id' => 'id']);
    }
}