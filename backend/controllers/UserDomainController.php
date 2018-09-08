<?php
namespace backend\controllers;
use Yii;
use backend\models\UserDomain;
use backend\models\ProviceCity;
use backend\models\Regions;
use yii\web\Controller;

class UserDomainController extends BaseController
{
    public $enableCsrfValidation = false;
    public function actionPullAgent()
    {        
        if(Yii::$app->request->post('tongbu'))
        {
            $domains=Yii::$app->api->request('basic/getAgent',[]);
            if($domains['ret']!=0)
            {
                echo $domains['msg'];exit;
            }
            foreach ($domains['result'] as $domain)
            {
                $data=UserDomain::findOne(['domain_id'=>$domain['domain_id']]);
                if($data)
                {
                    $data->agentname=$domain['agentname'];
                    $data->mobile=$domain['mobile'];
                    $data->region=$domain['region'];
                    $data->create_time=$domain['create_time'];
                    $data->uid=$domain['uid'];
                    $data->longitude=$domain['longitude'];
                    $data->latitude=$domain['latitude'];
                    $data->are_region_id=$domain['are_region_id'];
                    $data->save();
                   
                }else{
                    $model=new UserDomain();
                    $model->domain_id=$domain['domain_id'];
                    $model->agentname=$domain['agentname'];
                    $model->mobile=$domain['mobile'];
                    $model->region=$domain['region'];
                    $model->create_time=$domain['create_time'];
                    $model->uid=$domain['uid'];
                    $model->longitude=$domain['longitude'];
                    $model->latitude=$domain['latitude'];
                    $model->are_region_id=$domain['are_region_id'];
                    $model->save();
                }
                $query = Regions::findBySql('SELECT * FROM '.Regions::tableName().' where region_id = (SELECT p_region_id FROM '.Regions::tableName().' where region_id='.$domain['are_region_id'].' )')
                ->asArray()
                ->one();
                
                //$proviceCity = ProviceCity::find()->where(['city_id'=>$domain['domain_id']])->asArray()->one();
                $proviceCity=ProviceCity::findOne(['city_id'=>$domain['domain_id']]);
                if(count($proviceCity) == 0){
                	$model=new ProviceCity();
                	$model->province_id = $query['region_id'];
                	$model->province_name = $query['local_name'];
                	$model->city_id = $domain['domain_id'];
                	$model->city_name = $domain['region'];
                 	$model->department_id = 0;
                	$model->department_name = ' '; 
                	$model->save();
                }else{
                	$proviceCity->province_id = $query['region_id'];
                	$proviceCity->province_name = $query['local_name'];
                	$proviceCity->city_id = $domain['domain_id'];
                	$proviceCity->city_name = $domain['region'];
                 	$proviceCity->department_id = 0;
                	$proviceCity->department_name = ' '; 
                	$proviceCity->save();
                }
                
                
                
            }
            Yii::$app->session->setFlash('success','同步完成');
            return '<script>history.back()</script>';
        }
        
        return $this->render('pull');
    }
}