<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/3
 * Time: 16:42
 */
namespace official\api\help;

use app\foundation\Api;
use app\services\HelpService;

class FeedBackApi extends Api
{
    public function run()
    {
        $type = \Yii::$app->request->post('type_id');

        $service = HelpService::instance();
        $result = $service->getFeed($type);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];




    }





}