<?php
namespace app\foundation;

use yii\base\Component;
use app\models\Order;
use \SoapClient;
use app\models\Member;
use Yii;

class WmsProxy extends Component
{
    const SUCCESS_STATUS = 'Success';
    const ERROR_STATUS = 'Failure';
    
    public $secret = '';
    
    /*
     * 获取供应商的可配送车辆信息
     * @param \app\models\SupplierWms $wms  
     * @param $supplier_id  供货商ID
     * @return array  
     *   */
    public function getCars($wms)
    {
      //  var_dump($wms);exit;
        $params=[];
        return $this->request($wms,'execCapacityGetList', $params);
    }
    /* 
     * 进行送货单打印
     * @param \app\models\SupplierWms $wms  
     * @return array  
     *  
     *  */
    public function  deliveryNote($wms,$strTransportModel,$strListTransportTab)
    {
        $params['strTransportModel'] = json_encode($strTransportModel);
        $params['strListTransportTab'] = json_encode($strListTransportTab);
        return $this->request($wms,'InsertTransportAndTab', $params);
    }
    /* 
     * WMS 签收通知
     *  @param \app\models\SupplierWms $wms 
     *  @return array
     *  
     *  */
    public function paySign($wms,$order_id)
    {
      
        $params['ShopOrderID']=$order_id;
        return $this->request($wms,'SignOrder',$params);
      //return true;
    }
    
    private function request($wms, $method, $params)
    {

        $time = time();
        $params['Key'] = $time;
        $params['SecretKey'] = $this->token($time);
        $data=Yii::$app->api->request('basic/getSupplier',['supplierId'=>$wms['supplier_id']]);
        if($data['ret']==0)
        {
            $uname=$data[0]['username'];
        }else{
            return $result['ret']=30;
        }
        $params['uname'] = $uname;
      //  $params['uname'] = $wms->user->username;
        $result = [];

        try {
            $client = new SoapClient($wms['api_addr'], array('soap_version' => SOAP_1_2));
            $result = (array)call_user_func_array([$client, $method], array($params));
        //       file_put_contents('d:/log/123.log', print_r($result, true).print_r($params, true));
        }
        catch(\Exception $e)
        {
        //       file_put_contents('d:/log/qwe.log', 'Send Data:'.print_r($params, true).'<br />返回数据：'.print_r($e, true));
        }
        $retField = $method.'Result';
        $result = json_decode($result[$retField], true);
        /* 特别说明，ret==2 为获取车辆信息无可用车辆的传值， execCapacityGetList 接口返回*/
        
        if($result['ret'] == 0 )
        {
            $result['ret'] = true;
        }
        elseif($result['ret'] ===2)
        {
            $result['ret']=28;
        }
        else
        {
            $result['ret'] = false;
        }
        return $result;
    }
    
    private function token($time)
    {
        return md5($this->secret.$time);
    }
}