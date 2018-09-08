<?php
return [
    'adminEmail' => 'admin@example.com',
    'head_url'=>'_{{HEAD_URL}}_',
    'head_default'=>'/static/head_portrait/default.jpg',   
    'through'=>['85','113'],  //管理员的ID，获取最大管理权限
    'rank'=>[
        '1'=>'一线同事',
        '3'=>'子公司经理',
        '4'=>'部门经理',
        '30'=>'主公司经理',
    ],      //公司级别，暂时写在这里，如有需要请建表
    'jpush_appkey'=>'0f53d0578f8f8460e0b473a0',
    'jpush_secret'=>'a89fc774b77353eb65a00c02'
];

