<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?= GridView::widget([
    'dataProvider' => $unenrolled,
    'options' => [
        'class' => 'unenrolled'
    ],
    'columns' => [
        'album_no',
        'name',
        'surname',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    return Html::a('delete', $url);
                }
            ],
            'urlCreator' => function ($action, $model, $key, $index, $column) use ($lecture_date) {
                return Url::to(['deletepresence', 'participant_external_ref' => $model['id'], 'lecture_date_id' => $lecture_date->id]);
            }
        ],
    ]
]); ?>
