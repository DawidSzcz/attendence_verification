<?php
/**
 * Created by IntelliJ IDEA.
 * User: kazik
 * Date: 13.08.17
 * Time: 14:09
 */

namespace app\models;

class Participant extends \yii\db\ActiveRecord
{
    public function getPresences()
    {
        return $this->hasMany(Presence::class, ['participant_id' => 'id']);
    }

    public function getParticipations()
    {
        return $this->hasMany(Participation::class, ['participant_id' => 'id']);
    }

    public function isPresent($lecture_date_id)
    {
        return null !== Presence::findOne(['lecture_date_id' => $lecture_date_id, 'participant_id' => $this->id]);
    }

    public function isParticipant($lecture_id)
    {
        return null !== Participation::findOne(['lecture_id' => $lecture_id, 'participant_id' => $this->id]);
    }
}