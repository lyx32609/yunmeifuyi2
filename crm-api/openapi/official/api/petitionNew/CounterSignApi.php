<?php
namespace official\api\petitionNew;

use app\foundation\Api;
use app\services\CounterSignService;

/**
 * Class CounterSignApi
 * 加签接口
 * @package official\api\petitionNew
 */
class CounterSignApi extends Api
{
    public function run()
    {
        //当前登录人id
        $user_id = \Yii::$app->request->post('user_id');
        //签呈ID
        $petition_id = \Yii::$app->request->post('petition_id');
        //加签人id和意见 id,意见;id,意见
        $add_msg = \Yii::$app->request->post('add_msg');

        $service = CounterSignService::instance();
        $result = $service->CounterSign($user_id,$petition_id,$add_msg);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];


    }
}