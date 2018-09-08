<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/2
 * Time: 14:17
 */
namespace official\api\help;

use app\foundation\Api;
use app\services\HelpService;

/**
 * Class GetListApi
 * @package official\api\help
 * 获取帮助列表接口
 */
class GetListApi extends Api
{
    public function run()
    {
        $type = \Yii::$app->request->post('type_id');

        $service = HelpService::instance();
        $result = $service->getList($type);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}