<?php

namespace app\controllers;

use app\models\Transactions;
use app\models\Users;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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
        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            try {
                $userModel = Users::findByUsername($model->username);
                if ($userModel == null) {
                    $userModel = new Users();
                    $userModel->username = $model->username;
                    if (!$userModel->save()) {
                        throw new Exception('error create user');
                    }
                }
                $res = $model->login();
                if ($res)
                    return $this->redirect(['users']);
            } catch (Exception $e) {
            }
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionUsers()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Users::find()->andFilterWhere(['<>', 'id', Yii::$app->user->id]),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'id' => 'ASC'
                ]
            ]
        ]);
        return $this->render('users', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionTransaction($recipient_id)
    {
        $model = new Transactions();
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->recipient_id = $recipient_id;
                $model->sender_id = Yii::$app->user->id;
                if (!$model->save()) {
                    throw new Exception('error save model');
                }
                $recModel = $model->recipient;
                $recModel->balance += $model->cost;
                $senModel = $model->sender;
                $senModel->balance -= $model->cost;
                if (!$recModel->save(true, ['balance']))
                    throw new Exception('error rec');

                if (!$senModel->save(true, ['balance']))
                    throw new Exception('error sen');

                $transaction->commit();
                echo json_encode([
                    'status' => 'success']);
                exit();
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }
        echo Json::encode([
            'status' => 'content',
            'content' => $this->renderAjax('_tr_form', [
                'model' => $model
            ])
        ]);
    }

    public function actionProfile()
    {
        $userModel = Yii::$app->user->identity;
        $find = Transactions::find()->where('sender_id = :s OR recipient_id = :r', [':s' => $userModel->id, ':r' => $userModel->id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $find,
            'pagination' => false,

        ]);
        return $this->render('profile', [
            'userModel' => $userModel,
            'dataProvider' => $dataProvider
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
