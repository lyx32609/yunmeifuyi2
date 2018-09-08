<?php

return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'imageUploadRelativePath' => './uploads/image/', // 图片默认上传的目录
	'imageUploadSuccessPath' => 'uploads/image/', // 图片上传成功后，路径前缀
	'fileUploadRelativePath' => './uploads/file/', // 图片默认上传的目录
	'fileUploadSuccessPath' => 'uploads/file/', // 图片上传成功后，路径前缀
	'domain' => 'http://crm.xunmall.com/',
	// 'domain' => 'http://fulamei.admin.com/',//图片域名
	'domain_cpi' => 'http://crm.openapi.xunmall.com',
	'webuploader' => [
	    // 后端处理图片的地址，value 是相对的地址
	    'uploadUrl' => '/petition/test-upload',
	    // 多文件分隔符
	    'delimiter' => ',',
	    // 基本配置
	    'baseConfig' => [
	        //'defaultImage' => 'http://ceshi_admin.com/it/u=2056478505,162569476&fm=26&gp=0.jpg',
	        'disableGlobalDnd' => true,
	        'accept' => [
	            'title' => 'Images',
	            'extensions' => 'gif,jpg,jpeg,bmp,png',
	            'extensions_file' => 'pdf,xls,txt,doc,docx,xlsx',
	            'mimeTypes' => 'image/*','application/*'
	        ],
	        'pick' => [
	            'multiple' => false,
	        ],
	    ],
	],
];
