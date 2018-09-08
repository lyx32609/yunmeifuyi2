<?php

$params = require(__DIR__ . '/params.php');

return [
    'id' => 'official',
    'components' => [
        'user' => [
            'identityClass' => 'official\Identity',
            'enableAutoLogin' => false,
        ],
        'authManager'=>array(
            'class'=>'yii\rbac\DbManager',
            'db'=>'dbofficial',
            'itemTable'=>'auth_item',
            'itemChildTable'=>'auth_item_child',
            'assignmentTable'=>'auth_assignment',
        ),
        'wmsProxy' => require_once ROOT.'/config/wms.php',
    ],
    'params' => $params,
];
