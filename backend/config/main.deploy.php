<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),  
    require(__DIR__ . '/params.php')

);

return [
    'defaultRoute' => '/site/login',
    'id' => 'app-backend',
    'layout' => 'main',
    'language'=>'zh-CN',
    'timeZone'=>'Asia/Shanghai',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'aliases' => [
        '@mdm/admin' => '@vendor/rbac-admin',
    ],
    'modules' => [
        
        'rbac-admin' => [
            'class' => 'mdm\admin\Module',
            'controllerMap' => [
                'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    'userClassName' => 'backend\models\User',
                    'idField' => 'id'
                ],
                //				'other' => [
                    //					'class' => 'app\controllers\SiteController', // add another controller
                    //				],
            ],
            'layout' => 'left-menu',
            'mainLayout' => '@app/views/layouts/main.php',
            'menus' => [
                'assignment' => [
                    'label' => '分配权限' // change label
                ],
                'route' => null, // disable menu
            ],
        ],

    ],
'components' => [
        'api'=>[
                'class' => 'app\foundation\ApiHelper',
                'server' => '_{{OFFICE_HOST}}_',
                'appid' => '_{{OFFICE_APPID}}_',
                'secret' => '_{{OFFICE_SECRET}}_',
            ],
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => 'djfdklfdlfnmkldjfpeltjeipoorido',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
      
        'urlManager' => [
	        'class' => 'yii\web\UrlManager',
		    'enablePrettyUrl' => true,
		    'showScriptName' => false,
		    'rules' => [
		        ['class' => 'yii\rest\UrlRule', 'controller' => ['api/Data'
		        ]],
	    ],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/message',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // 使用数据库管理配置文件
        ],
        'as access' => [
            'class' => 'mdm\admin\components\AccessControl',
            'allowActions' => [
                'site/*',//允许访问的节点，可自行添加
                'admin/*',//允许所有人访问admin节点及其子节点
                'rbac-admin/*',
            ]
        ]
        
        
    ],
    'params' => $params,
];

?>
