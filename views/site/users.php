<?php

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="site-login">
        <h1><?= Html::encode($this->title) ?></h1>

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
                            return !Yii::$app->user->isGuest ? Html::a('Transaction', ['/site/transaction', 'recipient_id' => $model->id], [
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

<?php
Yii::$app->view->registerJs("
$(document).off('click', '.transaction-button'
    ).on('click', '.transaction-button', function (e) {
        e.preventDefault();
        send($(this).prop('href'));
    });
    var send = function (_url, _data) {
        $.ajax({
            url: _url,
            type: 'json',
            dataType: 'json',
            data: _data,
            success: function (res) {
                if (res.status == 'success') {
                    window.location.reload();
                }
                else {
                    $('#mainModal').html(res.content).modal();
                    $('#mainModal').off('submit','#tr-from'
                    ).on('submit','#tr-from',function(e){
                         e.preventDefault();
                         send(_url,$(this).serialize());
                    })
                }
            }
        })
    }
", \yii\web\View::POS_END);