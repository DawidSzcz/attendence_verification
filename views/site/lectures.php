<?php
    use \yii\bootstrap\ActiveForm;
    use dosamigos\datetimepicker\DateTimePicker;
?>


<?php $form = ActiveForm::begin([
        'id' => 'lecture-form',
        'layout' => 'horizontal',
        'action' => 'addlecture'
    ]);
?>


<?=$form->field($model, 'name')->textInput(); ?>

<?=$form->field($model, 'time')->widget(DateTimePicker::className(), [
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

<?=$form->field($model, 'lecturer_name')->dropDownList(\app\models\Lecturer::find()->select(['surname'])->asArray(true)->all());?>

<?=\yii\helpers\Html::submitButton('Dodaj', ['class' => 'btn btn-primary', 'name' => 'login-button']); ?>
<?php ActiveForm::end() ?>
