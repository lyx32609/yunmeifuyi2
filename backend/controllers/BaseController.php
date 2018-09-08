<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use backend\models\User;
use backend\models\UserRoute;
use backend\models\Menus;
use backend\models\CompanyCategroy;

class BaseController extends Controller{
    static public $userIds;
    public function beforeAction($action)
    {
    
        //系统参数里配置的超级管理员可以拥有一切权限
        $id = Yii::$app->user->identity->id;
        if(in_array($id, Yii::$app->params['through']))
        {
            self::$userIds = User::find()->select('id')->column();
            return true;
        }
        $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));//用户所具备的角色
        if(in_array("admin",$rules) || in_array($id, Yii::$app->params['through']) || in_array("hr",$rules))
        {
            return true;
        }
        else
        {
            throw new ForbiddenHttpException('您没有后台权限');
            return false;
        }  
        //判断当前登录的企业是否为试用账号
        $company_categrouy_id = Yii::$app->user->identity->company_categroy_id;
        $companyCategroy = CompanyCategroy::find()
                        ->where(['id'=>$company_categrouy_id])
                        ->asArray()
                        ->one();
        //如果为试用，判断试用时间是否超时        
        if($companyCategroy['failure'] == 1 && $companyCategroy['createtime'] + 10*60*60*24 < time())
        {
            throw new ForbiddenHttpException('试用时间已到期，请续费开通');
            return false;
        }
        
        
        //如果是总经理给予权限
        $id = Yii::$app->user->identity->id;
        if(Yii::$app->user->identity->rank == 30 ||  in_array($id, Yii::$app->params['through']))
        {
           self::$userIds = User::find()->select('id')->column();
           return true;
        }
        
        if(!in_array($id, Yii::$app->params['through']))
        {
            $userRoute = UserRoute::find()
                    ->where(['userid'=>$id])
                    ->asArray()
                    ->one();
            
            $ids = explode(',', $userRoute['menuids']);
            $userMenus = Menus::find()
                    ->select('url')
                    ->where(['id'=>$ids])
                    ->asArray()
                    ->all();
            $url = Yii::$app->request->getPathInfo();
            $arr = array();
            foreach ($userMenus as $k => $v)
            {
                $arr[$k] = $v['url'];
            }
            // echo "<pre>";
            // print_r($arr);
            // echo "</pre>";
            array_push($arr,'/user-location/department');
            array_push($arr,'/user-group/department');
            array_push($arr,'/user/group');
            array_push($arr,'/user/department');
            array_push($arr,'/user-location/synchronization');
            if(!in_array('/'.$url,$arr))
            {
                 throw new ForbiddenHttpException('您没有权限');
                 return false;
            }
        }        
        
        if(!\Yii::$app->user->identity)
        {
            $this->redirect(['site/login']);
            return false;
        }
        if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through']) || \Yii::$app->user->can('总管理员'))
        {
            self::$userIds = User::find()->select('id')->column();
            return true;
        }
        self::$userIds = User::find()->select('id')->column();
        return true;
        
        echo 5;exit();
        $m = $action->controller->id."/".$action->id;
        $can = \Yii::$app->user->can($m);
        if($can)
        {
            $rank = Yii::$app->user->identity->rank;
            $domain = Yii::$app->user->identity->domain_id;
            if($rank == 3)
            {
                self::$userIds=User::find()->select('id')->andWhere('domain_id='.$domain)->column();                 
            }
            elseif($rank == 30)
            {

                self::$userIds = User::find()->select('id')->column();    
            }
            else
            {
                throw new ForbiddenHttpException('您没有级别');
                return false;
            }
            return true;
        }
        else
        {
            throw new ForbiddenHttpException('您没有权限');      
            return false;
        }  
    }
}