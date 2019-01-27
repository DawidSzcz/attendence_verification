<?= \yii\grid\GridView::widget([
    'dataProvider' => $lecturers
]);?>

<?php $form = \yii\bootstrap\ActiveForm::begin(['action' => \yii\helpers\Url::to(['site/addlecturer'])]); ?>

<?= $form->field($model, 'name')->textInput() ?>


<?= $form->field($model, 'surname')->textInput() ?>

<?=\yii\helpers\Html::submitButton('Wyślij')?>

<?php $form->end();?>
