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
use app\models\Services\RaportGenerator;
use yii\base\Exception;
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
        $model->addLecture(\Yii::$app->user->id);

        return $this->redirect(Url::to(['index']));
    }


    public function actionAddlecturedate()
    {
        $id = \Yii::$app->request->post('id');
        $this->checkOwner(Lecture::findOne($id)->owner);

        $model = new LectureDate();
        $model->lecture_id = $id;
        $model->setAttributes(\Yii::$app->request->post('LectureDate'), false);
        $model->save();

        return $this->redirect(Url::to(['view', 'id' => $id]));
    }

    public function actionView($id)
    {
        $this->checkOwner(Lecture::findOne($id)->owner);
        $lecture = Lecture::findOne($id);

        $external_refs = array_map(function (Participant $participant) {
            return $participant->external_ref;
        }, $lecture->participants);

        $students = \Yii::$app->studentBase->retrieveStudentsByIds($external_refs);

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

        $presences = [];
        $unenrolled_presences = [];

        /** @var Participant $participant */
        foreach ($lecture->participants as $participant) {
            $presences[$participant->external_ref] = $participant->isPresent($id);
        }

        /** @var Participant $participant */
        foreach ($lecture_date->participants as $participant) {
            if (!$participant->isParticipant($lecture->id)) {
                $unenrolled_presences[] = $participant->external_ref;
            }
        }

        $students = \Yii::$app->studentBase->retrieveStudentsByIds(array_keys($presences));
        $unenrolled = \Yii::$app->studentBase->retrieveStudentsByIds($unenrolled_presences);

        return $this->render('view_date', [
            'lecture_date' => $lecture_date,
            'participants' => new ArrayDataProvider(['allModels' => $students]),
            'presences' => $presences,
            'unenrolled' => new ArrayDataProvider([
                'allModels' => $unenrolled
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

        $students = \Yii::$app->studentBase->retrieveStudentsByCardUids(FileHandler::getPresencesCardUids('file'));


        foreach ($students as $card_uid => $student) {
            if ([] === $student) {
                \Yii::$app->session->addFlash('notice', sprintf("Unknown student with card uid: %s", $card_uid));
            } else {

                $participant = Participant::findOne(['external_ref' => $student['id']]);

                if (null === $participant) {
                    $participant = new Participant();
                    $participant->external_ref = $student['id'];
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

    public function actionDeleteparticipant($participant_external_ref, $lecture_id)
    {
        $this->checkOwner(Lecture::findOne($lecture_id)->owner);

        Participation::findOne(['participant_id' => Participant::findOne(['external_ref' => $participant_external_ref])->id, 'lecture_id' => $lecture_id])->delete();

        return $this->redirect(Url::to(['view', 'id' => $lecture_id]));
    }

    public function actionAddpresence($participant_external_ref, $lecture_date_id)
    {
        $this->checkOwner(LectureDate::findOne($lecture_date_id)->lecture_id);
        $participant = Participant::findOne(['external_ref' => $participant_external_ref]);

        $presence = new Presence();
        $presence->participant_id = $participant->id;
        $presence->lecture_date_id = $lecture_date_id;

        $presence->save();

        return $this->redirect(Url::to(['viewdate', 'id' => $lecture_date_id]));
    }

    public function actionDeletepresence($participant_external_ref, $lecture_date_id)
    {
        $this->checkOwner(LectureDate::findOne($lecture_date_id)->lecture_id);
        $participant = Participant::findOne(['external_ref' => $participant_external_ref]);

        Presence::findOne(['participant_id' => $participant->id, 'lecture_date_id' => $lecture_date_id])->delete();

        return $this->redirect(Url::to(['viewdate', 'id' => $lecture_date_id]));
    }

    public function actionAddparticipant($album_no, $lecture_id)
    {
        $this->checkOwner(Lecture::findOne($lecture_id)->owner);
        $students = \Yii::$app->studentBase->retrieveStudentsByAlbumNos([$album_no]);
        $student = reset($students);

        if ([] === $student) {
            \Yii::$app->session->addFlash('notice', sprintf("Unknown student with album number: %s", $album_no));
        } else {
            if (null === ($participant = Participant::findOne(['external_ref' => $student['id']]))) {
                $participant = new Participant();
                $participant->external_ref = $student['id'];
                $participant->save();
            }

            $participation = new Participation();
            $participation->participant_id = $participant->getPrimaryKey();
            $participation->lecture_id = $lecture_id;

            $participation->save();
        }

        return $this->redirect(Url::to(['view', 'id' => $lecture_id]));
    }

    public function actionGeneratelist($lecture_id)
    {
        $lecture = Lecture::findOne($lecture_id);
        $this->checkOwner($lecture->owner);
        $students = \Yii::$app->studentBase->retrieveStudentsByIds(array_map(function (Participant $participant) {
            return $participant->external_ref;
        }, $participants = $lecture->participants));

        return \Yii::$app->response->sendContentAsFile(RaportGenerator::generateRaport($lecture, $students, $participants), $lecture->name . '.csv');
    }

    private function checkOwner(int $owner_id)
    {
        if ($owner_id !== \Yii::$app->user->id) {
            $this->redirect(['index']);
        }
    }

}