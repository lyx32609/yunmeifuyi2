<?php
namespace official\api\version;

use app\foundation\Api;
use app\services\VersionService;

/**
 * 获取最新版本
 * @return array 
 * @author lzk
 */
class VersionApi extends Api
{
    public function run()
    {
        $id = \Yii::$app->request->post('id');
        $data = VersionService::instance()->get($id);
        return ['result'=>$data];
    }
}