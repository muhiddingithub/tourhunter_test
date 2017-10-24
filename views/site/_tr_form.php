<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\models\Transactions */

?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">
                Transaction
            </h4>
        </div>

        <div class="modal-body">
            <?php $form = ActiveForm::begin([
                'id' => 'tr-from',
                'fieldConfig' => [
                    'template' => "<div class='col-sm-3 control-label'>{label}</div>\n<div class='input-group col-sm-9'>{input}</div>{error}",

                ]
            ]); ?>
            <?= $form->field($model, 'cost')->textInput() ?>
            <?= Html::submitButton('Send', ['class' => 'btn btn-success']) ?>
            <?= Html::button('Close', ['class' => 'btn bnt-danger', 'data' => [
                'dismiss' => 'modal'
            ]]) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

