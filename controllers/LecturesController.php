<?php

namespace app\controllers;

use app\components\StudentBase;
use app\models\Lecture;
use app\models\LectureDate;
use app\models\LectureForm;
use app\models\Participant;
use app\models\Participation;
use app\models\Presence;
use app\models\Services\FileHandler;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;

class LecturesController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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

        return parent::beforeAction($action);
    }


    public function actionIndex()
    {
        return $this->render('index', [
            'model' => new LectureForm(),
            'lectures' => new ActiveDataProvider(['query' => Lecture::find()->where(['owner' => \Yii::$app->user->id])])
        ]);
    }

    public function actionAddlecture()
    {
        $model = new LectureForm();

        $model->load(\Yii::$app->request->post());
        $model->addLecture();

        return $this->redirect(Url::to(['index']));
    }


    public function actionAddlecturedate()
    {
        $model = new LectureDate();
        $model->lecture_id = \Yii::$app->request->post('id');
        $model->setAttributes(\Yii::$app->request->post('LectureDate'), false);
        $model->save();

        return $this->redirect(Url::to(['view', 'id' => \Yii::$app->request->post('id')]));
    }

    public function actionView($id)
    {
        $this->checkOwner(Lecture::findOne($id)->owner);
        $lecture = Lecture::findOne($id);

        $album_nos = array_map(function (Participant $participant) {
            return $participant->nr_albumu;
        }, $lecture->participants);

        $students = \Yii::$app->studentBase->retrieveStudentsByAlbumNos($album_nos);

        return $this->render('view', [
            'lecture' => $lecture,
            'lecture_dates' => new ArrayDataProvider(['allModels' => $lecture->lectureDates]),
            'participants' => new ArrayDataProvider(['allModels' => $students])
        ]);

    }

    public function actionViewdate($id)
    {
        $lecture_date = LectureDate::findOne($id);
        $lecture = Lecture::findOne($lecture_date->lecture_id);

        $this->checkOwner($lecture->owner);

        return $this->render('view_date', [
            'lecture_date' => $lecture_date,
            'participants' => new ArrayDataProvider(['allModels' => $lecture->participants]),
            'unenrolled' => new ArrayDataProvider([
                'allModels' => array_filter($lecture_date->participants, function ($participant) use ($lecture) {
                    return !$participant->isParticipant($lecture->id);
                })
            ])
        ]);

    }

    public function actionUpdate()
    {
        $model = Lecture::findOne(\Yii::$app->request->post('id'));

        $this->checkOwner($model->owner);

        $model->setAttributes(\Yii::$app->request->post('Lecture'), false);
        $model->update();

        return $this->redirect(Url::to(['view', 'id' => \Yii::$app->request->post('id')]));
    }

    public function actionUpdatedate()
    {
        $model = LectureDate::findOne(\Yii::$app->request->post('id'));

        $this->checkOwner(Lecture::findOne($model->lecture_id)->owner);

        $model->setAttributes(\Yii::$app->request->post('LectureDate'), false);
        $model->update();

        foreach (FileHandler::getParticipantIds('file') as $album_no) {
            if (!empty(trim($album_no))) {

                $participant = Participant::findOne(['nr_albumu' => trim($album_no)]) ?? new Participant();

                if ($participant->isNewRecord) {
                    $participant->nr_albumu = trim($album_no);
                    $participant->name = 'Dawid Szczyrk';

                    $participant->save();
                }

                if (null === Presence::findOne(['lecture_date_id' => \Yii::$app->request->post('id'), 'participant_id' => $participant->getPrimaryKey()])) {
                    $presence = new Presence();
                    $presence->lecture_date_id = \Yii::$app->request->post('id');
                    $presence->participant_id = $participant->getPrimaryKey();
                    $presence->save();
                }
            }
        }
        return $this->redirect(Url::to(['viewdate', 'id' => \Yii::$app->request->post('id')]));
    }

    public function actionDelete($id)
    {
        $model = Lecture::findOne($id);

        $this->checkOwner($model->owner);

        $model->delete();

        return $this->redirect(Url::to(['index']));
    }

    public function actionDeletedate($id)
    {
        $lecture_id = LectureDate::findOne($id)->lecture_id;

        $this->checkOwner(Lecture::findOne($lecture_id)->owner);


        LectureDate::findOne($id)->delete();

        return $this->redirect(Url::to(['view', 'id' => $lecture_id]));
    }

    public function actionDeleteparticipant($participant_id, $lecture_id)
    {
        $this->checkOwner(Lecture::findOne($lecture_id)->owner);

        Participation::findOne(['participant_id' => $participant_id, 'lecture_id' => $lecture_id])->delete();

        return $this->redirect(Url::to(['view', 'id' => $lecture_id]));
    }

    public function actionAddpresence($participant_id, $lecture_date_id)
    {
        $this->checkOwner(LectureDate::findOne($lecture_date_id)->lecture_id);

        $presence = new Presence();
        $presence->participant_id = $participant_id;
        $presence->lecture_date_id = $lecture_date_id;

        $presence->save();

        return $this->redirect(Url::to(['viewdate', 'id' => $lecture_date_id]));
    }

    public function actionDeletepresence($participant_id, $lecture_date_id)
    {
        $this->checkOwner(LectureDate::findOne($lecture_date_id)->lecture_id);

        Presence::findOne(['participant_id' => $participant_id, 'lecture_date_id' => $lecture_date_id])->delete();

        return $this->redirect(Url::to(['viewdate', 'id' => $lecture_date_id]));
    }

    public function actionAddparticipant($nr_albumu, $lecture_id)
    {
        $this->checkOwner(Lecture::findOne($lecture_id)->owner);

        $participant = Participant::findOne(['nr_albumu' => $nr_albumu]) ?? new Participant();

        if ($participant->isNewRecord) {
            $students = \Yii::$app->studentBase->retrieveStudentsByAlbumNos([$nr_albumu]);
            $student_data = reset($students);
            $participant->nr_albumu = $nr_albumu;
            $participant->card_uid = $student_data['card_uid'];

            $participant->save();
        }

        $participation = new Participation();
        $participation->participant_id = $participant->getPrimaryKey();
        $participation->lecture_id = $lecture_id;

        $participation->save();

        return $this->redirect(Url::to(['view', 'id' => $lecture_id]));
    }

    public function actionGeneratelist($lecture_id)
    {
        $lecture = Lecture::findOne($lecture_id);
        $this->checkOwner($lecture->owner);

        $csv = ',';

        foreach ($lecture->participants as $participant) {
            $csv .= $participant->nr_albumu . ',';
        }
        $csv .= "\n";

        $csv .= ',';
        foreach ($lecture->participants as $participant) {
            $csv .= $participant->name . ',';
        }
        $csv .= "\n";

        foreach ($lecture->lectureDates as $lecture_date) {
            $csv .= $lecture_date->ts . ',';
            foreach ($lecture->participants as $participant) {
                $csv .= $participant->isPresent($lecture_date->id) ? '1,': '0,';
            }
            $csv .= "\n";
        }

        return \Yii::$app->response->sendContentAsFile($csv, $lecture_id . '.csv');
    }

    private function checkOwner(int $owner_id)
    {
        if ($owner_id !== \Yii::$app->user->id) {
            $this->redirect(['index']);
        }
    }

}