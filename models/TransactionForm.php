<?php
namespace app\models;

use yii\base\Model;
use yii\db\Exception;

class TransactionForm extends Model
{
    public $recipient_id;
    public $username;
    public $cost;

    public function rules()
    {
        return [
            [['username', 'recipient_id', 'cost'], 'required'],
            [['username'], 'string', 'max' => 50],
            ['username', 'trim'],
            [['recipient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['recipient_id' => 'id']],
            ['cost', 'integer', 'integerOnly' => true, 'min' => 1],
            ['recipient_id', 'compare', 'compareValue' => \Yii::$app->user->id, 'operator' => '!=', 'type' => 'number'],
            ['username', 'compare', 'compareValue' => \Yii::$app->user->identity->username, 'operator' => '!=', 'type' => 'string'],
            [['username', 'recipient_id', 'cost'], 'safe']
        ];
    }

    public function scenarios()
    {
        return [
            'hasUser' => ['recipient_id', 'cost'],
            'newUser' => ['username', 'cost']
        ];
    }

    public function transaction()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$this->validate()) {
                throw new \yii\base\Exception('not validate');
            }
            if ($this->scenario == 'newUser') {
                $userModel = Users::checkOrSignup($this->username);
                $this->recipient_id = $userModel->id;
            }
            $model = new Transactions();
            $model->recipient_id = $this->recipient_id;
            $model->sender_id = \Yii::$app->user->id;
            $model->cost = $this->cost;
            if (!$model->save()) {
                throw new Exception('error save model');
            }
            $recModel = $model->recipient;
            $recModel->balance += $this->cost;
            $senModel = $model->sender;
            $senModel->balance -= $this->cost;
            if (!$recModel->save(true, ['balance']))
                throw new Exception('error rec');

            if (!$senModel->save(true, ['balance']))
                throw new Exception('error sen');

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        return false;
    }

}