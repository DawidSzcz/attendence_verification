<?php
use app\models\LectureForm;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use kartik\time\TimePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $lectures,
    'columns' => [
        'name',
        'description',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{view} {delete}',
        ],
    ]
]); ?>


<?php $form = ActiveForm::begin([
    'id' => 'lecture-form',
    'layout' => 'horizontal',
    'action' => Url::to(['addlecture'])
]);
?>


<?= $form->field($model, 'name')->textInput(); ?>
<?= $form->field($model, 'description')->textInput(); ?>

<?= $form->field($model, 'grain')->dropDownList(array_combine(LectureForm::GRAINS, LectureForm::GRAINS), ['onchange' => "(function(event) {if('once' === event.target.value) {document.getElementById('range_picker').style.display = 'none'; document.getElementById('once_picker').style.display = 'block';} else {document.getElementById('range_picker').style.display = 'block'; document.getElementById('once_picker').style.display = 'none';} })(event)"]); ?>

<?= $form->field($model, 'first_date', ['options' => ['id' => 'range_picker', 'class' => 'form-group']])->widget(DateRangePicker::class, [
    'language' => 'en',
    'size' => 'ms',
    'attributeTo' => 'last_date',
    'clientOptions' => [
        'autoclose' => true,
        'format' => 'dd-M-yyyy',
        'todayBtn' => true
    ]]); ?>
<?= $form->field($model, 'once_date', ['options' => ['id' => 'once_picker', 'class' => 'form-group', 'style' => 'display: none']])->widget(DatePicker::class, [
    'language' => 'en',
    'size' => 'ms',
    'clientOptions' => [
        'autoclose' => true,
        'format' => 'dd-M-yyyy',
        'todayBtn' => true
    ]]); ?>
<?= $form->field($model, 'participants')->fileInput() ?>

<?= $form->field($model, 'time')->widget(TimePicker::class, [
    'name' => 'time',
    'pluginOptions' => [
        'showSeconds' => false
    ]
]); ?>

<?= \yii\helpers\Html::submitButton('Dodaj', ['class' => 'btn btn-primary', 'name' => 'lecture-button']); ?>

<?= Html::hiddenInput("LectureForm[owner_id]", \Yii::$app->user->id) ?>
<?php ActiveForm::end() ?>
