<?php

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <div class="row">
        <h1 class="pull-left"><?= Html::encode($this->title) ?></h1>
        <?php
        if (!Yii::$app->user->isGuest)
            echo Html::a('<i class="glyphicon glyphicon-plus"></i>' . 'Transaction', ['transaction'], ['class' => 'btn btn-success / pull-right / transaction-button']) ?>
    </div>
    <p>You may transactions thay:</p>


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
            'username',
            'balance',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{transaction}',
                'buttons' => [
                    'transaction' => function ($url, $model) {
                        return !Yii::$app->user->isGuest ? Html::a('<i class="glyphicon glyphicon-plus"></i>Transaction', ['/site/transaction', 'recipient_id' => $model->id], [
                            'title' => 'Transaction',
                            'class' => 'btn btn-xs btn-outline btn-success add-tooltip / transaction-button'
                        ]) : '';
                    }
                ]
            ],
        ],
    ]);
    ?>
</div>
<div class="modal" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

</div>
