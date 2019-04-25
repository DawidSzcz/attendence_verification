<?php
use dosamigos\datetimepicker\DateTimePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>


<a href="<?= Url::to(['view', 'id' => $lecture_date->lecture_id]); ?>"><h1><?= $lecture_date->lecture->name; ?></h1></a>
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

<?= \yii\grid\GridView::widget([
    'dataProvider' => $participants,
    'rowOptions' => function ($model, $key, $index, $grid) use ($lecture_date) {
        return [
            'class' => $model->isPresent($lecture_date->id) ? 'green' : 'red'
        ];
    }

]); ?>
<h1>Dodatkowi słuchacze</h1>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $unenrolled,
    'options' => [
        'class' => 'unenrolled'
    ]
]); ?>
