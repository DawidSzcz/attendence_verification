<?php

use app\models\LectureDate;
use dosamigos\datetimepicker\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?= GridView::widget([
    'dataProvider' => $lecture_dates,
    'columns' => [
        [
            'class' => 'yii\grid\Column',
            'header' => 'Date',
            'content' => function ($model, $key, $index, $column) {
                return $model->ts;
            }
        ],
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
<div class="row">
    <?php $form = ActiveForm::begin([
        'id' => 'lecture-date-form',
        'action' => Url::to(['addlecturedate'])
    ]);
    ?>
        <?= Html::hiddenInput('id', $lecture_id) ?>
        <div class="add-participant">
            <p class="label">Add new Date</p>
            <?= $form->field(new LectureDate(), 'ts')->widget(DateTimePicker::class, [
                    'language' => 'en',
                    'clientOptions' => [
                        'autoclose' => true,
                        'todayBtn' => true
                    ],
                    'options' => [
                        'tag' => false, // Don't wrap with "form-group" div
                    ]]
            )->label(false); ?>
            <?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary', 'name' => 'lecture-date-button']); ?>
        </div>
    <?php ActiveForm::end() ?>
</div>