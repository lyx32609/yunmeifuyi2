<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'static/css/tabs.css',
    ];
    public $js = [
        '/static/js/jquery-migrate.js',
        '/static/js/AdminLTE.min.js',
        '/static/js/jquery.validate.min.js',
        '/static/js/messages_zh.js',
        '/static/jquery-ui/jquery-ui-i18n.js',
        '/static/bootstrap/js/bootstrap.js',

    ];
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'backend\assets\AdminLTEAsset'
    ];
}
