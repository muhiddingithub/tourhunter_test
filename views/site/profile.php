<?php

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $userModel \app\models\Users */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <h2><span class="label label-info">Your Balance: <?= $userModel->balance ?></span></h2>
    <br>

    <?php
    echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{summary}{pager}',
        'tableOptions' => [
            'class' => 'table table-striped table-bordered',
            'id' => 'customers-grid'
        ],
        'columns' => [
            'id',
            [
                'attribute' => 'sender_id',
                'value' => function ($model) use ($userModel) {
                    return ($model->sender_id == $userModel->id) ? "You" : $model->sender->username;
                }
            ],
            [
                'attribute' => 'recipient_id',
                'value' => function ($model) use ($userModel) {
                    return ($model->recipient_id == $userModel->id) ? "You" : $model->recipient->username;
                }
            ],
            'cost'
        ],
    ]);
    ?>
</div>
