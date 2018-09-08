<?php
namespace app\services;

use app\foundation\Service;
use app\models\AppVersion;

class VersionService extends Service
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
        $row = AppVersion::findOne(['type'=>'0' , 'id'=>$id]);
        $res['versionCode'] = $row['code'];
        $res['downloadUrl'] = $row['download'];
        $res['versionMsg'] = $row['content'];
        return $res;
    }
    
}