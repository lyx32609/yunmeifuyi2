<?php

return [
    'adminEmail' => 'ldjbenben@sina.com',
    'max_time_difference' => '3000',
    'uploadUrl' => '_{{UPLOAD_URL}}_',   // 正式使用http://api.xunmall.com   测试使用 http://ngh.crm.openapi.xunmall.com
    'bannerImgUrl' => '_{{BANNER_URL}}_',
/*     'email_validata'=>'http://self.documen' */    
    'reflshTime' => '86400',
    'md5_key' =>'xunmall_api',
    'api'=>[
        'purchaser'=>[
            'domain'=>'_{{PURCHASER_HOST}}_'
        ],
        'official'=>[
//             'domain'=>'crm.openapi.xunmall.com' //正式
         'domain'=>'_{{OFFICIAL_HOST}}_' //本地测试(线上)
//          'domain'=>'ngh.crm.openapi.xunmall.com' //测试
        ],
        'inner'=>[
            'domain'=>'_{{INNER_HOST}}_'
        ],
        'supplier'=>[
            'domain'=>'_{{SUPPLIER_HOST}}_'
        ],
    ],
    'email_address'=>'_{{EMAIL_HOST}}_',
    'jpush_appkey'=>'0f53d0578f8f8460e0b473a0',
    'jpush_secret'=>'a89fc774b77353eb65a00c02'
];
