<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/2
 * Time: 13:50
 */
namespace official\api\help;

use app\foundation\Api;
use app\services\HelpService;

/**
 *
 * 获取帮助首页类型
 * Class GetTypeApi
 * @package official\api\help
 */
class GetTypeApi extends Api
{
    public function run()
    {
        $service = HelpService::instance();
        $result = $service->getType();

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];


    }
}