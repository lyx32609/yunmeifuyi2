<?php
namespace crm\api\user;

use app\foundation\Api;
use app\models\ApiCode;
use app\models\AccessToken;
use official\models\User;
use app\services\Service;
use official\Identity;
use app\services\VisitedApi;
use app\services\VisitedService;

class VisitedApi extends Api
{
    public function run()
    {
        $shopId = \Yii::$app->request->post('shopid');
        $staffId = \Yii::$app->user->identity->id;
//         file_put_contents("E:\id.log",print_r( \Yii::$app->user,true));
         VisitedService::instance()->add($shopId, $staffId);
        return [

        ];
    }
}
