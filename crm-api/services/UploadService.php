<?php
namespace app\services;
use Yii;
use app\foundation\Service;

class UploadService extends Service
{
    public function updateCertification(&$param,&$serialize_thumb)
    {
       return Yii::$app
                    ->dbmall
                    ->createCommand()
                    ->update('sdb_members',[
                        'certification_id_back' => $param['certification_id_back']['url'],
                        'certification_id_front' => $param['certification_id_front']['url'],
                        'certification_license' => $param['certification_license']['url'],
                        'certification_thumb' => $serialize_thumb,
                    ],"member_id = {$param['uid']}")
                    ->execute();
    }
}