<?php
namespace app\controllers;

use app\models\Api;
use app\models\ApiGroup;
use app\models\ApiClient;
use benben\helpers\NetworkHelper;

class SandboxController extends \yii\web\Controller
{
    public function actionPurchaser()
    {
        return $this->render('purchaser', [
            'apis' => $this->apis('purchaser'),
        ]);
    }
    
    public function actionOfficial()
    {
        return $this->render('purchaser', [
            'apis' => $this->apis('official'),
        ]);
    }
    
    public function actionInner()
    {
        return $this->render('purchaser', [
            'apis' => $this->apis('inner'),
        ]);
    }
    
    public function actionSupplier()
    {
        return $this->render('purchaser', [
            'apis' => $this->apis('supplier'),
        ]);
    }
    
    public function actionCrmOpenapi()
    {
        return $this->render('purchaser', [
            'apis' => $this->apis('official'),
        ]);
    }
    
    
    /**
     * 获取接口的相关参数表单字段
     * @param int $id 接口ID
     * @return string
     */
    public function actionParams($id)
    {
        $api = Api::findOne($id);
        $view = 'api/' . $api->module_id . '/' . str_replace('/', '_', $api->name);
        
        return $this->renderPartial($view);
    }
    
    public function actionRequest()
    {
/*         $client = ApiClient::findOne(\Yii::$app->request->post('appid'));
       
        if(!$client || $client->appkey == \Yii::$app->request->post('secret'))
        {
            
        }
        
        if(!empty($_REQUEST['secret']) && $appkey != $_REQUEST['secret'])
        {
            OpenApiResponse::error(OpenApiError::SIGNATURE_ERROR, 'secret error');
            exit();
        } */
        
        
        $appkey = \Yii::$app->request->post('secret') . '&';
        $data =  \Yii::$app->request->post('param');
        
        $api = Api::findOne($data['api']);

        if(!$api) echo '<h3>接口不存在</h3>';
        
        $data['api'] = $api->name;
        
        $data['appid'] = \Yii::$app->request->post('appid');
        $data['t'] = time();
        
        ksort($data);
        $sigStr = urldecode('&'.http_build_query($data));
        
        $data['s'] = base64_encode(hash_hmac('sha1', urlencode($sigStr), strtr($appkey, '-_', '+/'), true));
       
        $ret = NetworkHelper::makeRequest(\Yii::$app->params['api'][$api->module_id]['domain'], $data);
        echo '<br /><br />返回结果：<br /><br /><pre>'.$ret['msg'].'</pre>';
        
        echo '<h2>1、 构造源串</h2>';
        echo '源串：'.$sigStr.'<br />';
        echo 'URL编码后：'.$sigStr.'<br />';
        
        echo '<h2>2、构造密钥</h2>'.$appkey.'<br />';
        
        echo '<h2>3、生成签名值</h2>';
        echo "签名函数(PHP代码)：hash_hmac('sha1', urlencode(\"".$sigStr."\"), strtr(\"".$appkey."\", '-_', '+/'), true)<br />";
        echo '签名值：'.$data['s'].'<br /><br /><br />';
        
        echo '请求URL演示：<br />http://'.\Yii::$app->params['api'][$api->module_id]['domain'].'?'.NetworkHelper::makeQueryString($data);
        
    }
    
    private function apis($module)
    {
        return Api::find()
            ->leftJoin(ApiGroup::tableName().' group', Api::tableName().'.group_id = group.id')
            ->where([Api::tableName().'.module_id'=>$module])
            ->orderBy('group.priority desc, priority desc')
            ->all();
    }
}