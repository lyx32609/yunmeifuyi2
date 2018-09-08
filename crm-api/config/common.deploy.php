<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'version' => 1,
    'language' => 'zh-CN',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'bootstrap' => ['log'],
    'timeZone'=>'Asia/Shanghai',
    'components' => [
        'api'=>[
                'class' => 'app\foundation\ApiHelper',
                'server' => '_{{OFFICE_HOST}}_',
                'appid' => '_{{OFFICE_APPID}}_',
                'secret' => '_{{OFFICE_SECRET}}_',
            ],
        'ad_api'=>[
            'class' => 'app\foundation\ApiHelper',
            'server' => '_{{AD_HOST}}_',
            'appid' => '_{{AD_APPID}}_',
            'secret' => '_{{AD_SECRET}}_',
        ],
        'mmj_api'=>[
            'class' => 'app\foundation\ApiHelper',
            'server' => '_{{MMJ_HOST}}_',
            'appid' => '_{{MMJ_APPID}}_',
            'secret' => '_{{MMJ_SECRET}}_',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@root/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'helloxunmall123',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'dbofficial' => require(__DIR__ . '/../../common/config/db_official.php'),
        'setting' => [
            'class' => 'app\foundation\Setting',
            'settings' => [
                'seo.article.tag_max_num' => 5, // SEO设置，单个文章最多可设置的标签数
            ] 
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
