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
}