<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\models\UpdateForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use common\models\User;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'block', 'unblock'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
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
        $users = User::find()->where(['!=', 'id', Yii::$app->user->id])->all();
        return $this->render('index', ['users' => $users]);
    }

    public function actionUpdate($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            Yii::$app->session->setFlash('error', 'Incorrect user.');
            return Yii::$app->response->redirect(['site/index']);
        }
        $model = new UpdateForm();
        $model->fillForm($user);
        if ($model->load(Yii::$app->request->post()) && $model->update($id))
        {
            return $this->goBack();
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionBlock($id)
    {
        $res = User::findOne($id);
        if (!$res) {
            Yii::$app->session->setFlash('error', 'Incorrect user.');
            return Yii::$app->response->redirect(['site/index']);
        }
        $res->changeStatus(9);
        return $this->redirect('index');
    }

    public function actionUnblock($id)
    {
        $res = User::findOne($id);
        if (!$res) {
            Yii::$app->session->setFlash('error', 'Incorrect user.');
            return Yii::$app->response->redirect(['site/index']);
        }
        $res->changeStatus(10);
        return $this->redirect('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
            if (!array_key_exists('admin', $roles)) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('error', 'You are not admin.');
            } else {
                return $this->goBack();
            }
        }

        $model->password = '';
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
}
