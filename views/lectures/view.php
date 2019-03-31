<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use \app\models\LectureDate;
use dosamigos\datetimepicker\DateTimePicker;

?>

<?php $form = ActiveForm::begin([
    'action' => Url::to(['update'])
]) ?>
<?= $form->field($lecture, 'name') ?>
<?= $form->field($lecture, 'description') ?>
<?= Html::hiddenInput('id', $lecture->id)?>

<?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'lecture-button']); ?>
<?php ActiveForm::end() ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $lecture_dates,
    'columns' => [
        'ts',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{view} {delete}',
            'urlCreator' => function ($action, $model, $key, $index, $column) {
                if('view' === $action) {
                    return Url::to(['viewdate', 'id' => $model->id]);
                } else if ('delete' === $action) {
                    return Url::to(['deletedate', 'id' => $model->id]);
                }
                throw new \yii\base\Exception(sprintf('Undefined action [%s] in lecture view', $action));
            }
        ],
    ]
]); ?>

<h2>Add new Lecture Date</h2>
<?php $form = ActiveForm::begin([
    'id' => 'lecture-form',
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
<?= Html::hiddenInput('id', $lecture->id)?>
<?= \yii\helpers\Html::submitButton('Dodaj', ['class' => 'btn btn-primary', 'name' => 'lecture-date-button']); ?>
<?php ActiveForm::end() ?>
