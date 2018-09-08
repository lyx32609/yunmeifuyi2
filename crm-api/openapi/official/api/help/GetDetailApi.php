<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/2
 * Time: 14:28
 */
namespace official\api\help;

use app\foundation\Api;
use app\services\HelpService;

/**
 * Class GetDetailApi
 * @package official\api\help
 * 获取帮助详情
 */
class GetDetailApi extends Api
{
    public function run()
    {
        $list = \Yii::$app->request->post('list_id');
        $user_id = \Yii::$app->request->post('user_id');


        $service = HelpService::instance();
        $result = $service->getDetail($user_id, $list);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];


    }

}