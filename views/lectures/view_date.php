<?php

use dosamigos\datetimepicker\DateTimePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = $lecture_date->ts;
$this->params['breadcrumbs'][] = [
        'label' => $lecture_date->lecture->name,
        'url' => sprintf('/lectures/view/%d', $lecture_date->lecture_id)
];
$this->params['breadcrumbs'][] = $this->title;

?>
<h1><?= $this->title; ?></h1>
<?php $form = ActiveForm::begin([
    'action' => Url::to(['updatedate']),
    'options' => ['enctype' => 'multipart/form-data']
]) ?>
<?= $form->field($lecture_date, 'ts')->widget(DateTimePicker::class, [
    'language' => 'en',
    'size' => 'ms'
]); ?>
<?= Html::hiddenInput('id', $lecture_date->id); ?>
<?= Html::label('File structuture: album number seprated with semicolons'); ?>
<?= Html::fileInput('file') ?>

<?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'lecture-button']); ?>
<?php ActiveForm::end() ?>

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
<h1>Additional Attenders</h1>
<?= \yii\grid\GridView::widget([
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
