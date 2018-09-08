<?php

$config = [
    'defaultApp' => 'purchaser',
    'components' => [
        'response' => [
            'format' => 'json',
            'formatters'=>[
                \yii\web\Response::FORMAT_JSON => 'app\foundation\JsonFormatter'
            ],
        ],
    ],
];

return $config;
