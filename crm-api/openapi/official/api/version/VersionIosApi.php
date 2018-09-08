<?php
namespace official\api\version;

use app\foundation\Api;
use app\services\VersionIosApiService;

/**
 * 获取IOS最新版本
 * @return array 
 * @author lzk
 */
class VersionIosApi extends Api
{
    public function run()
    {
        $id = \Yii::$app->request->post('id');
        $data = VersionIosApiService::instance()->get($id);
        return ['result'=>$data];
    }
}