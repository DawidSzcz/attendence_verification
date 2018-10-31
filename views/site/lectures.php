<?php
    use \yii\bootstrap\ActiveForm;
    use dosamigos\datetimepicker\DateTimePicker;
?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $lectures
]);?>


<?php $form = ActiveForm::begin([
        'id' => 'lecture-form',
        'layout' => 'horizontal',
        'action' => 'addlecture'
    ]);
?>


<?=$form->field($model, 'name')->textInput(); ?>

<?=$form->field($model, 'date_time')->widget(DateTimePicker::className(), [
    'language' => 'en',
    'size' => 'ms',
    'template' => '{input}',
    'pickButtonIcon' => 'glyphicon glyphicon-time',
    'inline' => true,
    'clientOptions' => [
        'autoclose' => true,
        'format' => 'dd MM yyyy - HH:ii P',
        'todayBtn' => true
    ]]);?>

<?=$form->field($model, 'classroom')->textInput(); ?>
<?php Yii::error(var_export(array_reduce(\app\models\Lecturer::find()->all(), function ($aux, $lecturer) {
    $aux[$lecturer->id] = $lecturer->name . ' ' . $lecturer->surname;
    return $aux;
},  []), true)); ?>
<?=$form->field($model, 'lecturer_id')->dropDownList(array_reduce(\app\models\Lecturer::find()->all(), function ($aux, $lecturer) {
    $aux[$lecturer->id] = $lecturer->name . ' ' . $lecturer->surname;
    return $aux;
},  []));?>

<?=\yii\helpers\Html::submitButton('Dodaj', ['class' => 'btn btn-primary', 'name' => 'login-button']); ?>
<?php ActiveForm::end() ?>
