<?= \yii\grid\GridView::widget([
    'dataProvider' => $lecturers
]);?>

<?php echo __DIR__; $form = \yii\bootstrap\ActiveForm::begin(['action' => 'addlecturer']); ?>

<?= $form->field($model, 'name')->textInput() ?>


<?= $form->field($model, 'surname')->textInput() ?>

<?=\yii\helpers\Html::submitButton('Wyślij')?>

<?php $form->end();?>
