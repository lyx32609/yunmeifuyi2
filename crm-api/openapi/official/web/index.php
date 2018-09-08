<?php
error_reporting(E_ALL ^ E_NOTICE);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../../vendor/autoload.php');
require(__DIR__ . '/../../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../../config/bootstrap.php');
require(__DIR__ . '/../Application.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../config/common.php'),
    require(__DIR__ . '/../../../config/main-api.php'),
    require(__DIR__ . '/../config/main.php')
);

(new \official\Application($config))->run();

