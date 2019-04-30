<?php
use app\models\LectureDate;
use dosamigos\datetimepicker\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php $form = ActiveForm::begin([
    'action' => Url::to(['update'])
]) ?>
<?= $form->field($lecture, 'name') ?>
<?= $form->field($lecture, 'description') ?>
<?= Html::hiddenInput('id', $lecture->id) ?>

<?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'lecture-button']); ?>
<?php ActiveForm::end() ?>


<h1>Terminy wykładu:</h1>
<?= GridView::widget([
    'dataProvider' => $lecture_dates,
    'columns' => [
        'ts',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{view} {delete}',
            'urlCreator' => function ($action, $model, $key, $index, $column) {
                if ('view' === $action) {
                    return Url::to(['viewdate', 'id' => $model->id]);
                } else if ('delete' === $action) {
                    return Url::to(['deletedate', 'id' => $model->id]);
                }
                throw new \yii\base\Exception(sprintf('Undefined action [%s] in lecture view', $action));
            }
        ],
    ]
]); ?>

<h1>Uczestnicy wykładu</h1>

<?= GridView::widget([
    'dataProvider' => $participants,
    'columns' => [
        'id',
        'name',
        'nr_albumu',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{delete}',
            'urlCreator' => function ($action, $model, $key, $index, $column) use ($lecture){
                if ('delete' === $action) {
                    return Url::to(['deleteparticipant', 'participant_id' => $model->id, 'lecture_id' => $lecture->id]);
                }
                throw new \yii\base\Exception(sprintf('Undefined action [%s] in lecture view', $action));
            }
        ],
    ]
]); ?>

<h2>Add new Lecture Date</h2>
<?php $form = ActiveForm::begin([
    'id' => 'lecture-date-form',
    'action' => Url::to(['addlecturedate'])
]);
?>
<?= $form->field(new LectureDate(), 'ts')->widget(DateTimePicker::class, [
    'language' => 'en',
    'size' => 'ms',
    'clientOptions' => [
        'autoclose' => true,
        'todayBtn' => true
    ]]); ?>
<?= Html::hiddenInput('id', $lecture->id) ?>
<?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary', 'name' => 'lecture-date-button']); ?>
<?php ActiveForm::end() ?>

<h2>Add new Participant</h2>
<?php $form = ActiveForm::begin([
    'id' => 'lecture-date-form',
    'action' => Url::to(['addparticipant']),
    'method' => 'get'
]); ?>

<?= Html::label('Numer albumu: '); ?>
<?= Html::textInput('nr_albumu'); ?>
<?= Html::hiddenInput('lecture_id', $lecture->id) ?>
<?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary', 'name' => 'lecture-date-button']); ?>

<?php ActiveForm::end() ?>


<?= Html::a('Generuj listę obecności', Url::to(['generatelist', 'lecture_id' => $lecture->id]), ['class' => 'btn btn-primary']); ?>
