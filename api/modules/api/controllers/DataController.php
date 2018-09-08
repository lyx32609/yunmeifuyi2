<?php 
namespace api\modules\api\controllers;

use yii\rest\ActiveController;
use \yii\base\Controller;
use Yii;
use api\modules\api\models\User;
use api\modules\api\models\UserSign;
use api\modules\api\models\SignSn;
use api\modules\api\models\UserWorkSign;
use official\api\user\UserScanSignApi;
use function GuzzleHttp\Psr7\str;
class DataController  extends Controller
{
    
    public $modelClass = 'common\models\User';
    
    private $_data = '';
	private $_sn = '';
	
	/**
	 * 获取数据指令
	 *
	 */
	public function actionGet()
	{
	    $sign_sn = SignSn::findOne(['id' => 1]);
	    if(!$sign_sn){
	        $sign_sn = new SignSn();
	        $sign_sn->id = 1;
	        $sign_sn->sn = Yii::$app->request->get('sn');
	        $sign_sn->save();
	    }
	   $cmd = $this->getUser(1); //查看是否有需要添加的人员
		if(!$cmd){
		    $cmd = $this->getUser(3); //查看是否有需要删除的人员
		    if(!$cmd){
		        $cmd = '';
		    }
		}
		return json_encode($cmd);
	}
	
	/**
	 * 接收设备上传的数据
	 *
	 */
	public function actionPost(){
// 	    $result = $this->getUser(3);
// 	    return json_encode([
// 	        'status' => '1',
// 	        'info' => 'ok',
// 	        'data' => [
// 	            'id' => 1006,
// 	            'do' => 'delete',
// 	            'data' => [
// 	                'user' => 'fingerprint',
// 	                'face' => 'headpic',
// 	                'clockin' => 'pic',
	                
// 	            ],
// 	            'ccid' => $result
// 	        ]
	        
// 	    ]);
	    $this->_sn = Yii::$app->request->get('sn');
	    $psot = Yii::$app->request->post('not_push_num');
	    $reqtime = Yii::$app->request->get('requesttime');
		$datas= json_decode(trim(file_get_contents('php://input')), true);
		//查询设备号
// 		file_put_contents('assets/one.txt', print_r(file_get_contents('php://input'), true), true);
		$dev = SignSn::findOne(['id' => 1]);
		$companyid = $dev['id'];
		$dremark = $dev['sn'];
// 		if(count($datas) > 1){
// 		    return $this->ok([$data['id'], $datas[1]['id']]);
		        
// 		} else{
// 		    return $this->ok($data['id']);
// 		    }
		if(!empty($datas)) {
		    foreach ($datas as $data){
			   switch($data['data']){
					case 'user'://员工添加修改记录
						//{id:1,data:"user",ccid:123456,name:"name",passwd:"md5",auth:0,deptid:0,card:123456,fingerprint:["fptemp0","fptemp1"],face:["base64","base64","base64","base64","base64","base64","base64","base64","base64"],headpic:"base64"}
					
// 						//保存指纹
// 						if(is_array($data['fingerprint'])) {

// 						}
// 						//保存卡号
// 						if($data['card']) {
							
// 						}
						
// 						//保存人脸
// 						if(!empty($data['face'])) {
							
// 						}
// 						//保存照片
// 						if(!empty($data['headpic'])) {
							
// 						}
						$okid[] = $data['id'];

						break;
						
					case 'fingerprint'://指纹数据
						if(empty($data['ccid']) || empty($data['fingerprint'])) continue;
						//{id:2,data:"fingerprint",ccid:123456,fingerprint:["base64","base64"]}
						$fpdata = array();
						if(is_array($data['fingerprint'])) {
							//保存两个指纹
						}else{
							//保存单个指纹
						}
						$okid[] = $data['id'];
						
						break;
					
					case 'face'://人脸数据
						if(empty($data['ccid']) || empty($data['face'])) continue;
						//{ id:3, data:"face",ccid:123456,face:["base64","base64","base64","base64","base64","base64","base64","base64","base64"]}
						//保存人脸

						$okid[] = $data['id'];
						
						break;
					
					case 'deleteface'://删除人脸
						//{ id:8, data:"deleteface",ccid:[123456,654654]}
						if(empty($data['ccid'])) continue;

						$okid[] = $data['id'];
						break;
						
					case 'headpic'://员工头像
						if(empty($data['ccid']) || empty($data['headpic'])) continue;
						//{id:4,data:"headpic",ccid:123456,headpic:"base64"}
						//保存照片
						
						$okid[] = $data['id'];

						break;
						//员工打卡记录
					case 'clockin':
						if(empty($data['ccid'])) continue;
						//{id:2,data:"clockin",ccid:123456,time:"2015-09-05 18:05:21",verify:0,pic:"base64"}
						//TODO:保存打卡记录
						
						$user_work = UserWorkSign::findOne(['is_staff' => 2, 'company_id' => 1]); //查询上午上下班时间
						$sign = new UserSign();
					    $user = User::findOne(['id' => $data['ccid']]);
						$sign->user = $data['ccid'];
						$data =  strtotime(date("Y-m-d"),time());
					    $work_time = strtotime($data['time']) - $data;
					    $type = 0;
					    if($user_work->status == '1'){
					        if($work_time < $user_work->morning_to_work){ //如果打卡时间小于上午上班时间
					            $sign->is_late = 0;
					            $sign->is_late_time = '0';
					        } else {
					            $result = UserSign::find()
        					            ->where(['user' => $user])
        					            ->andWhere(['between', 'time', $data, time()])
        					            ->asArray()
        					            ->one();
					            if($result){
					                $sign->is_late = 0;
					                $sign->is_late_time = '0';
					            } else {
					                $sign->is_late = 0;
					                $sign->is_late_time = strval(ceil(($work_time - $user_work->morning_to_work) / 60));
					            }
					        }
					    } else {
					        if(($work_time < $user_work->morning_to_work) || (($work_time > $user_work->morning_go_work) && ($work_time < $user_work->after_to_work))){ //如果打卡时间小于上午上班时间 或者 打卡时间大于上午下班时间且小于下午上班时间
					            $sign->is_late = 0;
					            $sign->is_late_time = '0';
					        } else if(($work_time > $user_work->morning_to_work) && ($work_time < $user_work->morning_go_work)) {//如果打卡时间大于上午上班时间且小于上午下班时间
					            $result = UserSign::find()
					            ->where(['user' => $user])
					            ->andWhere(['between', 'time', $data, $data + $work_time])
					            ->asArray()
					            ->one();
					            if($result){ //如果零点到打卡时间有打卡记录
					                $sign->is_late = 0;
					                $sign->is_late_time = '0';
					            } else {//如果零点到打卡时间没有打卡记录
					                $sign->is_late = 0;
					                $sign->is_late_time = strval(ceil(($work_time - $user_work->morning_to_work) / 60));
					            }
					        } else if ($work_time > $user_work->after_to_work) {//如果打卡时间大于下午上班时间
					            $result = UserSign::find()
        					            ->where(['user' => $user])
        					            ->andWhere(['between', 'time', $data + $user_work->morning_go_work, $data + $user_work->after_to_work])
        					            ->asArray()
        					            ->all();
					            if(count($result) > 2){ //如果中午打卡时间大于2次
					                $sign->is_late = 0;
					                $sign->is_late_time = '0';
					            } else { //如果中午打卡次数小于2
					                $result_one = UserSign::find()
        					                ->where(['user' => $user])
        					                ->andWhere(['between', 'time', $data + $user_work->after_to_work, $data + $work_time])
        					                ->asArray()
        					                ->one();
					                if($result_one){
					                    $sign->is_late = 0;
					                    $sign->is_late_time = '0';
					                } else {
					                    $sign->is_late = 0;
					                    $sign->is_late_time = strval(ceil(($work_time - $user_work->after_to_work) / 60));
					                }
					            } 
					            
					        }
					    }
					    $sign->type = $type;
					    $sign->source_type = 2;
						$sign->time = strtotime($data['time']);
						$sign->path = '打卡机';
						$sign->company_id = $user->company_categroy_id;
						if(!$sign->save()){
						    
						    return $this->error($data['id']);
						}
						
						//保存现场照片
						
						$okid[] = $data['id'];
                        
						break;
						
					case 'return'://接收设备数据处理结果
						//{ id:7,data:"return",return:[{id:1001,result:0},{id:1002, result:0},{id:1003, result:"shell return msg"}] }
						//更新下发数据状态
					    $user_id = $this->getUser(2, $data['return'][0]['id']);
					    $okid[] = $data['id'];
						
						break;
						
					case 'info'://接收设备信息
						//{id:6,data:"info",model:"QY-168", rom:"1.1.2",app:"1.0.3", space:54821, memory:1000,user:300,fingerprint:150,face:200,headpic:300,clockin:2054,pic:2054}

						$okid[] = $data['id'];
						
						break;
						
					case 'unbound'://解除绑定
						//清除未处理数据状态
						//删除设备绑定关系
					   
						$okid[] = $data['id'];
						break;
						
					default:
						break;
				}
			}
			//[1,2,3,5,4,7]
			return $this->ok($okid);
		}else{
			return $this->error([0]);
		}
		
	}
    public function getUser($type, $user_id = null)
    {
        $user_sn = SignSn::findOne(['id' => 1]);
        if($type == 3){
            $result = User::find()
                ->select(['id', 'name', 'password'])
                ->where(['company_categroy_id' => 1])
                ->andWhere(['is_staff' => '0'])
                ->andWhere(['cid' => $user_sn->sn])
                ->asArray()
                ->all();
            if(!$result){
                return false;
            }
            $list = [
                'status' => '1',
                'info' => 'ok',
                'data' => [
                    'id' => $result[0]['id'],
                    'do' => 'delete',
                    'data' => ["user", "fingerprint", "face", "headpic", "clockin", "pic"],
                    'ccid' => []
                ]
            ];
            for($i = 0; $i < count($result); $i++){
                $list['data']['ccid'][$i] = $result[$i]['id'];
            }
            return $list;
        } else  if($type == 1){
            $result = User::find()
            ->select(['id', 'name', 'password'])
            ->where(['company_categroy_id' => 1])
            ->andWhere(['is_staff' => '1'])
            ->andWhere(['<>', 'cid', $user_sn->sn])
            ->asArray()
            ->all();
            if(!$result){
                return false;
            }
            $list = [
                'status' => '1',
                'info' => 'ok',
                'data' => []
            ];
            for($i = 0; $i < count($result); $i++){
                $list['data'][$i]['id'] = $result[$i]['id'];
                $list['data'][$i]['do'] = 'update';
                $list['data'][$i]['data'] = 'user';
                $list['data'][$i]['ccid'] = $result[$i]['id'];
                $list['data'][$i]['name'] = $result[$i]['name'];
                $list['data'][$i]['passwd'] = $result[$i]['password'];
                $list['data'][$i]['card'] = '';
                $list['data'][$i]['deptid'] = 0;
                $list['data'][$i]['auth'] = 0;
            }
            return $list;
        } else if($type == 2){
            $user = User::findOne(['id' => $user_id]);
            if($user->cid && $user->is_staff == 0){
                $result = User::find()
                        ->select(['id'])
                        ->where(['is_staff' => 0])
                        ->andWhere(['company_categroy_id' => 1])
                        ->asArray()
                        ->all();
                for($i = 0; $i < count($result); $i++){
                    $user = User::findOne(['id' => $result[$i]['id']]);;
                    $user->cid = '0';
                    $user->save();
                }
                return true;
            }
            $result = User::find()
                    ->select(['id', 'name', 'password'])
                    ->where(['company_categroy_id' => 1])
                    ->andWhere(['is_staff' => '1'])
                    ->andWhere(['<>', 'cid', $user_sn->sn])
                    ->asArray()
                    ->all();
            for($i = 0; $i < count($result); $i++){
                $user = User::findOne(['id' => $result[$i]['id']]);
                $user->cid = $this->_sn;
                $user->save();
                if(!$user->save()){
                  return false;
                } 
            }
            return true;
        } 
        
    }
	/**
	 * 获取unixtime
	 *
	 */
	public function actionUnixtime(){
		$time = time();
		$diffutc = date('Z');
		$unixtime = $time - $diffutc;
		$ret = array(
			'timezone' => 'UTC',
			'unixtime' => $unixtime,
			'datetime' => date('Y-m-d H:i:s',$unixtime)
		);
		return json_encode([$ret,'ok',1]);
	}
	/**
	 * 如果成功返回值
	 * @param unknown $id
	 */
	public function ok($id)
	{
	    
	        $result = [
	            'status' => 1,
	            'info' => 'ok',
	            'data' => $id
	        ];
	    
	    
	    return json_encode($result);
	}
	/**
	 * 如果失败返回值
	 * @param unknown $id
	 */
	public function error($id)
	{
	    $result = [
	        'status' => 0,
	        'info' => 'data invalid',
	        'data' => $id
	    ];
	    return json_encode($result);
	}
	public function checkBind()
	{
	    $result = [
	       'status' => 1,
	        'info' => 'ok',
	        'data' => [
	            'id' => 0,
	            'do' => 'update',
	            'data' => 'config',
	            'name' => '云媒股份',
	            'companyid' => 1,
	            'max' => 3000,//目前设计最大值
	            'function' => 65535,
	            'delay' => 20,
	            'errdelay' => 50,
	            'interval' => 5,
	            'timezone' => 'GMT+08:00',
	            'encrypt' => 0,
	            'expired' => 0
	        ]
	    ];
	    return json_encode($result);
	}
	/**
	 * 处理上午早退
	 */
	public function actionSign()
	{
	    $user = User::find()
	           ->select(['id', 'company_categroy_id'])
	           ->where(['is_staff' => 1])
	           ->asArray()
	           ->all();
	    $start = time();
	    for($i = 0; $i < count($user); $i++){
	        $user_sign = $this->checkSign($user[$i]['id'], $user[$i]['company_categroy_id']);
	    }
	    $end = time();
	    echo  $result = 'OK AND time:' . ($end - $start);
	}
	private function checkSign($user, $company_id)
	{
	    $yester = strtotime(date("Y-m-d",strtotime("-1 day")));
	    $user_work = UserWorkSign::findOne(['is_staff' => 2, 'company_id' => $company_id]);
	    if(!$user_work){
	        return false;
	    }
	    if($user_work->status == '1'){
	        return false;
	    }
	    $result = UserSign::find()
	            ->select(['id', 'time'])
	            ->andWhere(['user' => $user])
	            ->andWhere(['between', 'time', $yester + $user_work->morning_go_work, $yester + $user_work->after_to_work])
	            ->andWhere(['is_late' => 0])
	            ->orderBy('time desc')
	            ->asArray()
	            ->all();
	    if(!$result){ //如果上午下班到上午上班之前有记录
	        $result_one = UserSign::find()
    	            ->select(['id', 'time'])
    	            ->andWhere(['user' => $user])
    	            ->andWhere(['between', 'time', $yester, $yester + $user_work->morning_go_work])
    	            ->andWhere(['is_late' => 0])
    	            ->orderBy('time desc')
    	            ->asArray()
    	            ->all();
	        if(count($result_one) < 2){//如果今日凌晨到上午下班之前最近的一次签退没有
	           return false;
	        } else {
	            $user_sign = UserSign::findOne(['id' => $result_one[0]['id']]);
	            $user_sign->is_late = 2;
	            $user_sign->is_late_time = strval(ceil(($yester + $yester + $user_work->morning_go_work - $result_one['time']) / 60));
	            $user_sign->save();
	        }
	    } else {
	        return false;
	    }
	}
	/**
	 * 处理下午早退
	 */
	public function actionSignGo()
	{
	    $user = User::find()
        	    ->select(['id', 'company_categroy_id'])
        	    ->where(['is_staff' => 1])
        	    ->asArray()
        	    ->all();
	    $start = time();
	    for($i = 0; $i < count($user); $i++){
	        $user_sign = $this->checkSignGo($user[$i]['id'], $user[$i]['company_categroy_id']);
	    }
	    $end = time();
	    echo  $result = 'OK AND time:' . ($end - $start);
	}
	private function checkSignGo($user, $company_id)
	{
	    $data_time = strtotime(date("Y-m-d"),time());
	    $yester = strtotime(date("Y-m-d",strtotime("-1 day")));
	    $user_work = UserWorkSign::findOne(['is_staff' => 2, 'company_id' => $company_id]);
	    if(!$user_work){
	        return false;
	    }
	    $result = UserSign::find()
	            ->select(['id', 'time'])
	            ->andWhere(['user' => $user])
	            ->andWhere(['between', 'time', $yester + $user_work->after_go_work, $data_time])
	            ->andWhere(['is_late' => 0])
	            ->orderBy('time desc')
	            ->asArray()
	            ->one();
	    if($result){
	        return false;
	    } 
	    $result_one = UserSign::find()
        	    ->select(['id', 'time'])
        	    ->andWhere(['user' => $user])
        	    ->andWhere(['between', 'time', $yester + $user_work->after_to_work, $yester + $user_work->after_go_work])
        	    ->andWhere(['is_late' => 0])
        	    ->orderBy('time desc')
        	    ->asArray()
        	    ->one();
	    if(!$result_one){
	        return false;
	    }
	    $user_sign = UserSign::findOne(['id' => $result_one['id']]);
	    $user_sign->is_late = 2;
	    $user_sign->is_late_time = strval(ceil(($yester + $user_work->after_go_work - $result_one['time']) / 60));
	    $user_sign->save();
	}
}