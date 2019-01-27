<?php

namespace app\controllers;

use app\models\Lecture;
use app\models\Lecturer;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\LectureForm;
use app\models\LecturerForm;
use yii\data\ActiveDataProvider;
use \yii\helpers\Url;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionLectures()
    {
        return $this->render('lectures', [ 'model' => new LectureForm(), 'lectures' => new ActiveDataProvider(['query' => Lecture::find()])]);
    }

    public function actionAddlecture()
    {
        $model = new LectureForm();

        $model->load(Yii::$app->request->post());
        $model->addLecture();

        return $this->redirect(Url::to(['lectures']));
    }

    public function actionLecturers()
    {
         return $this->render('lecturers', [ 'model' => new LecturerForm(), 'lecturers' => new ActiveDataProvider(['query' => Lecturer::find()])]);
    }

    public function actionAddlecturer()
    {
        $model = new LecturerForm();
        Yii::error(var_export($model, true));

        $model->load(\Yii::$app->request->post());
        Yii::error(var_export($model, true));
        if ($model->validate()) {
            $model->addLecturer();
        }

        return $this->redirect(Url::to(['lecturers']));
    }
}
