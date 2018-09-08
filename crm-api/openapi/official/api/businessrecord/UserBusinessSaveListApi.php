<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\UserBusinessService;

/**
 * 查询新增业务
 * @return msg："保存成功",result ：""
 * @author lzk
 */
class UserBusinessSaveListApi extends Api
{
    public function run()
    {
        $staff_num = \Yii::$app->user->id;
        $service = UserBusinessService::instance();
        $res = $service->getUserBusinessSaveList($staff_num);
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return [msg=>'查询成功',result=>$res];
    }
}