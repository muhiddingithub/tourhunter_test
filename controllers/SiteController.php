<?php

namespace app\controllers;

use app\models\TransactionForm;
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

class SiteController extends Controller
{
    public $defaultAction = 'users';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'transaction', 'profile'],
                'rules' => [
                    [
                        'actions' => ['logout', 'transaction', 'profile'],
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
        ];
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
                Users::checkOrSignup($model->username);
                if ($model->login())
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

    public function actionTransaction($recipient_id = null)
    {
        $model = new TransactionForm();
        if (is_null($recipient_id)) {
            $model->scenario = 'newUser';
        } else {
            $model->scenario = 'hasUser';
            $model->recipient_id = $recipient_id;
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->transaction()) {
                echo json_encode([
                    'status' => 'success'
                ]);
                exit();
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
            'sort' => [
                'defaultOrder' => [
                    'created_dt' => SORT_DESC
                ]
            ]

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
