<?php
/**
 * Created by IntelliJ IDEA.
 * User: kazik
 * Date: 13.08.17
 * Time: 14:09
 */

namespace app\models;

class LectureDate extends \yii\db\ActiveRecord
{
    public function getPresences()
    {
        return $this->hasMany(Presence::class, ['lecture_date_id' => 'id']);
    }

    public function getLecture()
    {
        return $this->hasOne(Lecture::class, ['id' => 'lecture_id']);
    }

    public function getParticipants()
    {
        return $this->hasMany(Participant::class, ['id' => 'participant_id'])
            ->viaTable('presence', ['lecture_date_id' => 'id']);
    }
}