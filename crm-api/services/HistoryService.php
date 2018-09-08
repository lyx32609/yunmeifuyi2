<?php
namespace app\services;

use app\foundation\Service;
use app\models\Member;
use app\models\ShopNote;
use app\models\Supplier;
class HistoryService extends Service
{
   /**
     * 获取店铺历史记录
     * @parm int shop_id店铺id
     * @return array ['id'=>历史记录id, 'conte'=>内容, 'date'=>时间]
     * @author lzk
     */
    public function getHistoryRecords($shop_id,$belong)
    {
        $data = $this->historyRecords($shop_id,$belong);
        return $data;
    }
    
  
    /**
     * 获取店铺历史记录
     * @parm int shop_id店铺id
     * @return array ['id'=>历史记录id, 'conte'=>内容, 'date'=>时间]
     * @author lzk
     */
    public function historyRecords($shop_id,$belong)
    {
        if($belong=='1')
        {
            $shop=\Yii::$app->api->request('basic/getMember',['member_id'=>$shop_id]);
            if(!$shop)
            {
                $this->setError('店铺不存在');
                return false;
            }   
        }
        else if($belong=='2')
        {
            $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId'=>$shop_id]);
            if(!$supplier)
            {
                $this->setError('店铺不存在');
                return false;
            }
        }
        
        
        $data = array();
        $row = $this->shopRecords($shop_id,$belong);
        for ($i = 0; $i < count($row); $i++) {
            $data[$i]['id'] = $row[$i]['id'];
            $data[$i]['conte'] = $row[$i]['conte'];
            $data[$i]['date'] = (date('Y-m-d',$row[$i]['time'])?date('Y-m-d',$row[$i]['time']) : '');
            $data[$i]['user'] = $row[$i]['user'];
            $data[$i]['imag'] = explode(',', $row[$i]['imag']);
            for($j = 0; $j < count($data[$i]['imag']); $j++){
                $data[$i]['imag'][$j] = \Yii::$app->params['uploadUrl'].'/'. $data[$i]['imag'][$j];
        }
        }
        return $data;
    }
   
   /**
     * 获取店铺历史记录
     * @parm int shop_id店铺id
     * @return array ['id'=>历史记录id, 'conte'=>内容, 'date'=>时间]
     * @author lzk
     */
    public function shopRecords($shop_id,$belong)
    {
            $row = (new \yii\db\Query())
            ->select('id,conte,time')            ->from(ShopNote::tableName()) 
            ->andWhere('shop_id=:shop_id',[':shop_id'=>$shop_id])
            ->andWhere('belong=:belong',[':belong'=>$belong])
            ->orderBy('time desc')
            ->all(\Yii::$app->dbofficial);
           
           return $row;
    }
    
    
    /**
     * 获取店铺历史记录2.1版本
     * @parm int shop_id店铺id
     * @return array ['id'=>历史记录id, 'conte'=>内容, 'date'=>时间,'name'=>业务员名字,'img'=>图片地址]
     * @author lzk
     */
    public function getHistoryRecordsData($shop_id,$belong)
    {
        if($belong=='1')
        {
            $shop=\Yii::$app->api->request('basic/getMember',['member_id'=>$shop_id]);
            if(!$shop)
            {
                $this->setError('店铺不存在');
                return false;
            }   
        }
        else if($belong=='2')
        {
            $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId'=>$shop_id]);
            if(!$supplier)
            {
                $this->setError('店铺不存在');
                return false;
            }
        }
        $data = array();
        $row = (new \yii\db\Query())
            ->select('b.id,b.conte,b.time,a.name,b.imag')
            ->from(ShopNote::tableName().b) 
		    ->leftJoin('off_user as a', 'b.user = a.username')
            ->andWhere('b.shop_id=:shop_id',[':shop_id'=>$shop_id])
            ->andWhere('b.belong=:belong',[':belong'=>$belong])
            ->orderBy('b.time desc')
            ->all(\Yii::$app->dbofficial);
        //print_r($row);exit();
        for ($i = 0; $i < count($row); $i++) {
            $data[$i]['id'] = $row[$i]['id'];
            $data[$i]['conte'] = $row[$i]['conte'];
            $data[$i]['date'] = (date('Y-m-d',$row[$i]['time'])?date('Y-m-d',$row[$i]['time']) : '');
            $data[$i]['user'] = $row[$i]['user'];
            $data[$i]['name'] = $row[$i]['name'];
            $data[$i]['imag'] = \Yii::$app->params['uploadUrl'].'/'. $row[$i]['imag'];
/*             $data[$i]['imag'] = explode(',', $row[$i]['imag']);
            for($j = 0; $j < count($data[$i]['imag']); $j++){
            } */
        }
        return $data;
    
    }
    
    
    
}