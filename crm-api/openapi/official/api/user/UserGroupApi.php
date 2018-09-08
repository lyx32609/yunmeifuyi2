<?php
/**
 * Created by 付腊梅.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 上午 11:27
 */
namespace official\api\user;

use app\foundation\Api;
use app\services\UserGroupService;

/**
 *  获取分组信息列表（新）
 * @return array
 * @author qizhifei
 */
class UserGroupApi extends Api
{
    public function run()
    {
        $area = \Yii::$app->request->post('area');
        $city = \Yii::$app->request->post('city');
        $department = \Yii::$app->request->post('department');
        $service = UserGroupService::instance();
        $result = $service->getUserGroup($area,$city,$department);
        if($result === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}