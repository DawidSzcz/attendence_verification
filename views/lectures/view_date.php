<?php

use dosamigos\datetimepicker\DateTimePicker;
use yii\bootstrap\Collapse;
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
])->label('Date'); ?>
<?= Html::hiddenInput('id', $lecture_date->id); ?>
<?= Html::label('File structuture: album number seprated with semicolons'); ?>
<?= Html::fileInput('file', null, [
    'class' => 'form-control-file'
]); ?>

<?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'lecture-button']); ?>
<?php ActiveForm::end() ?>
<?= Collapse::widget([
    'items' => [
        [
            'label' => 'Presence List',
            'content' => $this->render('/_partials/_presence_list', [
                'lecture_date' => $lecture_date,
                'presences' => $presences,
                'participants' => $participants
            ]),
            'options' => [
                'class' => 'lecture-collapse'
            ]
        ],
        [
            'label' => 'Unenrolled students',
            'content' => $this->render('/_partials/_presence_unenrolled', [
                'unenrolled' => $unenrolled,
                'lecture_date' => $lecture_date
            ]),
            'options' => [
                'class' => 'lecture-collapse'
            ]
        ],
    ]
]); ?>

