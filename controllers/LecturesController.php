<?php

namespace app\controllers;

use app\models\Lecture;
use app\models\LectureDate;
use app\models\LectureForm;
use app\models\Participant;
use app\models\Presence;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;

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

    public function beforeAction($action)
    {
        \Yii::$app->view->params['menu'] = \Yii::$app->user->isGuest
            ? [
                ['label' => 'Home', 'url' => ['/site/login']],
                ['label' => 'About', 'url' => ['/site/about']],
                ['label' => 'Contact', 'url' => ['/site/contact']]
            ]
            : [
                ['label' => 'Lectures', 'url' => ['/lectures']],
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

    public function actionView($id)
    {
        return $this->render('view', [
            'lecture' => Lecture::findOne($id),
            'lecture_dates' => new ArrayDataProvider(['allModels' => Lecture::findOne($id)->lectureDates])
        ]);

    }

    public function actionViewdate($id)
    {
        return $this->render('view_date', [
            'lecture_date' => LectureDate::findOne($id),
            'participants' => new ArrayDataProvider(['allModels' => LectureDate::findOne($id)->participants])
        ]);

    }

    public function actionUpdate()
    {
        $model = Lecture::findOne(\Yii::$app->request->post('id'));
        $model->setAttributes(\Yii::$app->request->post('Lecture'), false);
        $model->update();

        return $this->redirect(Url::to(['view', 'id' => \Yii::$app->request->post('id')]));
    }

    public function actionUpdatedate()
    {
        $model = LectureDate::findOne(\Yii::$app->request->post('id'));
        $model->setAttributes(\Yii::$app->request->post('LectureDate'), false);
        $model->update();

        $file = UploadedFile::getInstanceByName('file');

        foreach(explode(';', file_get_contents($file->tempName)) as $album_no) {
            if(!empty(trim($album_no))) {
                $participant = new Participant();
                $participant->nr_albumu = trim($album_no);
                $participant->name = 'Dawid Szczyrk';
                $participant->save();

                $presence = new Presence();
                $presence->lecture_date_id = \Yii::$app->request->post('id');
                $presence->participant_id = $participant->getPrimaryKey();
                $presence->save();
            }
        }

        return $this->redirect(Url::to(['viewdate', 'id' => \Yii::$app->request->post('id')]));
    }

    public function actionDelete($id)
    {
        Lecture::findOne($id)->delete();
        return $this->render('index', ['model' => new LectureForm(), 'lectures' => new ActiveDataProvider(['query' => Lecture::find()])]);
    }

    public function actionDeletedate($id)
    {
        LectureDate::findOne($id)->delete();

        return $this->render('view', [
            'lecture' => Lecture::findOne($id),
            'lecture_dates' => new ArrayDataProvider(['allModels' => Lecture::findOne($id)->lectureDates])
        ]);
    }

}