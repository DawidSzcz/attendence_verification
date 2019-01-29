<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;

use app\models\LectureForm;
use app\models\Lecture;

class LecturesController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction()
    {
        \Yii::$app->view->params['menu'] = \Yii::$app->user->isGuest
            ? [
                ['label' => 'Home', 'url' => ['/site/login']],
                ['label' => 'About', 'url' => ['/site/about']],
                ['label' => 'Contact', 'url' => ['/site/contact']]
            ]
            : [
                ['label' => 'Lectures', 'url' => ['/site/lectures']],
                ['label' => 'About', 'url' => ['/site/about']],
                ['label' => 'Contact', 'url' => ['/site/contact']],
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . \Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            ];

        return true;
    }


    public function actionIndex()
    {
        return $this->render('index', ['model' => new LectureForm(), 'lectures' => new ActiveDataProvider(['query' => Lecture::find()])]);
    }

    public function actionAddlecture()
    {
        $model = new LectureForm();

        $model->load(\Yii::$app->request->post());
        $model->addLecture();

        return $this->redirect(Url::to(['index']));
    }

}