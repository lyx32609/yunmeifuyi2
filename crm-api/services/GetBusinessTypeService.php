<?php
/**
 * Created by 付腊梅
 * User: Administrator
 * Date: 2017/4/20 0003
 * Time: 下午 4:53
 */
namespace app\services;
use app\foundation\Service;
use app\models\BusinessType;
use Yii;
class GetBusinessTypeService extends Service
{
    /**
        获取企业类型列表
     * @return [type]  array
     */
    public function getBusinessType()
    {
        $result = BusinessType::find()
                ->select(['type_id', 'type_name'])
                ->asArray()
                ->all();
        if(!$result)
        {
            $this->setError('暂时没有企业类型');
            return false;
        }
        return $result;

    }
}