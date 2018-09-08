<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserGroup;
use app\benben\DateHelper;
use app\models\ShopNote;
use app\models\UserBusiness;
use app\models\UserBusinessNotes;

class StaffListService extends Service
{
    /**
     * 人员查询
     * @return array
     */
    public function getStaffList()
    {
    
        $data = $this->staffList();
        return $data;
    }
    
  
   /**
     * 人员查询
     * @return array
     */
    public function staffList()
    {
            $data = array();
            $list = array();
            $row = $this->listData();
            if(!$row)
            {
                $this->setError('分组获取失败');
                return false;
            }
            for ($i = 0; $i < count($row); $i++) {
                $data[$i]['groupid'] = $row[$i]['groupid'];
                $data[$i]['groupName'] = $row[$i]['groupName'];
                $res = $this->listStaff($row[$i]['groupid']);
                for ($j = 0; $j < count($res); $j++) {
                    $list[$j]['staffNote']=$this->select($res[$j]['staffed']);
                    $list[$j]['staffed'] = $res[$j]['staffed'];
                    $list[$j]['staffName'] = $res[$j]['staffName'];
                }
                $data[$i]['groupStaff'] = empty($list)?[]:$list;
                unset($list);
            }
            return $data;
    }
    
    
    /*
     * 查询业务人员当日汇报的提交情况
     *  早上 8:30   晚上 5：30 （10月至次年4月）   6:00  （5月至次年9月）
     *  */
    private function select($user_id)
    {
        $s=0;
        $user=User::findOne(['id'=>$user_id]);
        if(!$user)
        {
            $this->setError('用户不存在');
            return false;
        }
        $time=$_SERVER['REQUEST_TIME'];
        $today_start=DateHelper::getTodayStartTime();
        $today_end=DateHelper::getTodayEndTime();
        $real_time=$time-$today_start;   //获取当天的实时时间戳
        $goToWork=$today_start+8.5*3600;
        $m=DateHelper::getMonth();
        if($m>=5&&$m<=9)
        {
            $goOffWord=$today_start+18*3600;
        }else{
            $goOffWord=$today_start+17.5*3600;
        }
        
        $start_note=ShopNote::find()->andWhere(['user'=>$user->username])->andWhere(['between','time',$today_start,$goToWork])->one();            
        $start_user_business=UserBusiness::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$today_start,$goToWork])->one();            
        $start_user_business_notes=UserBusinessNotes::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$today_start,$goToWork])->one();           
        if(!$start_note&&!$start_user_business&&!$start_user_business_notes)
        {
            $s=$s+1;
        }
        
        if($time-$goOffWord>0)
        {
            $end_note=ShopNote::find()->andWhere(['user'=>$user->username])->andWhere(['between','time',$goOffWord,$today_end])->one();
            $end_user_business=UserBusiness::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$goOffWord,$today_end])->one();
            $end_user_business_notes=UserBusinessNotes::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$goOffWord,$today_end])->one();
            if(!$end_note&&!$end_user_business&&!$end_user_business_notes)
            {
                $s=$s+2;
            }
        }
        return $s;
    }
   
    /**
     * 获取分组情况
     * @return 
     */
    public function listData()
    {
        $rank=\Yii::$app->user->identity->rank;
        if($rank==3)
        {
            $domainId = \Yii::$app->user->identity->domainId;
            $row = (new \yii\db\Query())
            ->select('g.id as groupid, g.name as groupName')
            ->from(UserGroup::tableName().' g')
            ->andWhere('g.id != 13')
            ->andWhere('domain_id='.$domainId)
            ->all(\Yii::$app->dbofficial);
        }elseif($rank==30){
            $row = (new \yii\db\Query())
            ->select('g.id as groupid, g.name as groupName')
            ->from(UserGroup::tableName().' g')
            ->andWhere('g.id != 13')
            ->all(\Yii::$app->dbofficial);
        }
        return $row;
    }
    /**
     * 获取分组业务员数据
     * @return
     */
    public function listStaff($gid)
    {
        $data = (new \yii\db\Query())
        ->select('u.id as staffed, u.name as staffName')
        ->from(User::tableName().' u')
        ->where('u.group_id = :gid',[':gid'=>$gid])
        ->all(\Yii::$app->dbofficial);
        return $data;
    }
}