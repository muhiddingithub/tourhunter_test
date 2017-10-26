<?php
namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;


class ViewAsset extends AssetBundle
{
    public $sourcePath = '@app/web/resources';
    public $js = [
        'js/view.js'
    ];
    public $jsOptions = [
        'position' => View::POS_END
    ];
}
