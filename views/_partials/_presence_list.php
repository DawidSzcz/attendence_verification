<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?= GridView::widget([
    'dataProvider' => $participants,
    'columns' => [
        'album_no',
        'name',
        'surname',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{delete} {add}',
            'buttons' => [
                'add' => function ($url, $model, $key) use ($presences) {
                    return !$presences[$model['id']] ? Html::a('add', $url) : '';
                },
                'delete' => function ($url, $model, $key) use ($presences) {
                    return $presences[$model['id']] ? Html::a('delete', $url) : '';
                }
            ],
            'urlCreator' => function ($action, $model, $key, $index, $column) use ($presences, $lecture_date) {
                return $presences[$model['id']] ?
                    Url::to(['deletepresence', 'participant_external_ref' => $model['id'], 'lecture_date_id' => $lecture_date->id]) :
                    Url::to(['addpresence', 'participant_external_ref' => $model['id'], 'lecture_date_id' => $lecture_date->id]);
            }
        ],
    ],

    'rowOptions' => function ($model, $key, $index, $grid) use ($presences) {
        return [
            'class' => $presences[$model['id']] ? 'green' : 'red'
        ];
    }

]); ?>