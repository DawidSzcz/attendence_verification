<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

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
