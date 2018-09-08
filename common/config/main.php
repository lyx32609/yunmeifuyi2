<?php

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache', 
        ],
        'dbofficial' => require 'db_official.php',
 
    ],
];

