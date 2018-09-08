<?php
namespace app\services;

use app\foundation\Service;
use app\models\AppVersion;
use app\models\IosVersion;
class VersionIosApiService extends Service
{
    /**
     * 获取最新版本
     * @param int $id 
     * @return array
     */
    public function get($id)
    {
        $data = $this->getVersion($id);
        return $data;
    }
    
   
   /**
     * 获取最新版本
     * @param int $id 
     * @return array
     */
    public function getVersion($id)
    {
        $res = array();
        $id = in_array($id, array(1,2)) ? $id : 1;
        $row = IosVersion::findOne(['id'=>$id]);
        return $row;
    }
    
}