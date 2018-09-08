<?php 
namespace backend\modules\api\controllers;

use yii\rest\ActiveController;
use Yii;
use backend\modules\api\User;
use backend\modules\api\UserSign;
use backend\modules\api\SignSn;
use backend\modules\api\UserWork;
class DataController extends ActiveController
{
    public $modelClass = 'common\models\User';
    
    private $_data = '';
	private $_sn = '';
	
	public function __construct(){
		parent::__construct();

		$this->_sn = Yii::$app->request->get('sn');
		
		if(empty($this->_sn)) {
			return json_encode(0,'sn is empty',0);
		}
	}
	
	/**
	 * 获取数据指令
	 *
	 */
	public function actionGet(){
		$dev = SignSn::findOne(['id' => 1]);
		//默认配置参数
		$def = array(
			'id' => 0,
			'do' => 'update',
			'data' => 'config',
			'name' => '群英云考勤',
			'company' => '群英云考勤',
			'companyid' => 0,
			'max' => 3000,//目前设计最大值
			'function' => 65535,
			'delay' => 20,
			'errdelay' => 50,
			'interval' => 5,
			'timezone' => 'GMT+08:00',
			'encrypt' => 0,
			'expired' => 0
		);
			
		if(empty($dev)) {//未添加的设备
			//TODO:未添加设备可以自动添加入设备库
		}else{
			//读取要下发到设备的数据
			$cmd = $this->getUser($this->_sn);
		}
		
		if(empty($cmd)) {//没有要下发的数据则返回配置信息
			//读取设备配置，如果配置信息为空返回默认配置
			$cmd = $def;
		}
		
		return json_encode($cmd,'ok',1);
	}
	
	/**
	 * 接收设备上传的数据
	 *
	 */
	public function actionPost(){
		$reqtime = Yii::$app->request->get('requesttime');
				
		if(empty($reqtime)) {
			return json_encode(0,'requesttime is empty',0);
		}
		
		$this->_data = trim(file_get_contents('php://input'));

		//查询设备号
		$dev = SignSn::findOne(['id' => 1]);
		$companyid = $dev['id'];
		$dremark = $dev['sn'];
		
		$data = json_decode($this->_data,true);
		
		if(!empty($data)) {
			/*
			[
			{ id:1, data:"user",ccid:123456,name:"张三",passwd:"md5",auth:0,deptid:0,card:123456},
			{ id:2, data:"fingerprint",ccid:123456,fingerprint:["base64","base64"]},
			{ id:3, data:"face",ccid:123456,face:["base64","base64","base64","base64","base64","base64","base64","base64","base64"]},
			{ id:8, data:"deleteface",ccid:[123456,654654]},
			{ id:4, data:"headpic",ccid:123456,headpic:"base64"},
			{ id:5, data:"clockin", ccid:123456,time:"2015-09-05 18:05:21",verify:0,pic:"base64"},
			{ id:6, data:"info", model:"QY-168",rom:"1.1.2",app:"1.0.3",space:54821, memory:1000,user:300,fingerprint:150,face:200,headpic:300,clockin:2054,pic:2054},
			{ id:7, data:"return",ok:[1001,1002,1003,1004]},
			]
			*/
			
			foreach ($data as $d){
				switch($d['data']){
					case 'user'://员工添加修改记录
						//{id:1,data:"user",ccid:123456,name:"name",passwd:"md5",auth:0,deptid:0,card:123456,fingerprint:["fptemp0","fptemp1"],face:["base64","base64","base64","base64","base64","base64","base64","base64","base64"],headpic:"base64"}
						if(empty($d['ccid'])) continue;
						//TODO:员工数据保存到员工表
						$user = User::findOne(['id' => $d['ccid']]);
						$user->cid = $this->_sn;
						if(!$user->save()){
						    return json_encode($d['ccid'],'data invalid',1);
						}
						//保存指纹
						if(is_array($d['fingerprint'])) {

						}
						//保存卡号
						if($d['card']) {
							
						}
						
						//保存人脸
						if(!empty($d['face'])) {
							
						}
						//保存照片
						if(!empty($d['headpic'])) {
							
						}
						$okid[] = $d['id'];

						break;
						
					case 'fingerprint'://指纹数据
						if(empty($d['ccid']) || empty($d['fingerprint'])) continue;
						//{id:2,data:"fingerprint",ccid:123456,fingerprint:["base64","base64"]}
						$fpdata = array();
						if(is_array($d['fingerprint'])) {
							//保存两个指纹
						}else{
							//保存单个指纹
						}
						$okid[] = $d['id'];
						
						break;
					
					case 'face'://人脸数据
						if(empty($d['ccid']) || empty($d['face'])) continue;
						//{ id:3, data:"face",ccid:123456,face:["base64","base64","base64","base64","base64","base64","base64","base64","base64"]}
						//保存人脸

						$okid[] = $d['id'];
						
						break;
					
					case 'deleteface'://删除人脸
						//{ id:8, data:"deleteface",ccid:[123456,654654]}
						if(empty($d['ccid'])) continue;

						$okid[] = $d['id'];
						break;
						
					case 'headpic'://员工头像
						if(empty($d['ccid']) || empty($d['headpic'])) continue;
						//{id:4,data:"headpic",ccid:123456,headpic:"base64"}
						//保存照片
						
						$okid[] = $d['id'];

						break;
					
					case 'clockin'://员工打卡记录
						if(empty($d['ccid'])) continue;
						//{id:2,data:"clockin",ccid:123456,time:"2015-09-05 18:05:21",verify:0,pic:"base64"}
						//TODO:保存打卡记录
						$go_work = UserWork::findOne(['id' => 1]);
						$sign = new UserSign();
					    $user = User::findOne(['id' => $d['ccid']]);
						$sign->user = $d['ccid'];
					    $work_time = strtotime($d['time']) / 60 * 60 * 24;
					    if($work_time > $go_work->go_work){
					        $sign->is_late = 1;
					        $sign->is_late_time = ($work_time - $go_work->go_work) / 60;
					    }
						$sign->time = strtotime($d['time']);
						$sign->path = '打卡机';
						$sign->company_id = $user->company_categroy_id;
						if(!$sign->save()){
						    return json_encode($d['ccid'],'data invalid',1);
						}
						//保存现场照片
						
						$okid[] = $d['id'];

						break;
						
					case 'return'://接收设备数据处理结果
						//{ id:7,data:"return",return:[{id:1001,result:0},{id:1002, result:0},{id:1003, result:"shell return msg"}] }
						//更新下发数据状态
						
						$okid[] = $d['id'];
						
						break;
						
					case 'info'://接收设备信息
						//{id:6,data:"info",model:"QY-168", rom:"1.1.2",app:"1.0.3", space:54821, memory:1000,user:300,fingerprint:150,face:200,headpic:300,clockin:2054,pic:2054}

						$okid[] = $d['id'];
						
						break;
						
					case 'unbound'://解除绑定
						//清除未处理数据状态
						//删除设备绑定关系
						
						$okid[] = $d['id'];
						break;
						
					default:
						break;
				}
			}
			//[1,2,3,5,4,7]
			return json_encode($okid,'ok',1);
		}else{
			return json_encode(0,'data invalid',1);
		}
	}
    public function getUser()
    {
        $result = User::find()
                ->select(['id', 'name', 'password'])
                ->where(['company_categroy_id' => 1])
                ->andWhere(['cid', ['cid' => null]])
                ->asArray()
                ->all();
       
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
            $list['deptid'][$i]['card'] = 0;
            $list['data'][$i]['auth'] = 0;
        }
        return $list;
    }
	/**
	 * 获取unixtime
	 *
	 */
	public function unixtime(){
		$time = time();
		$diffutc = date('Z');
		$unixtime = $time - $diffutc;
		$ret = array(
			'timezone' => 'UTC',
			'unixtime' => $unixtime,
			'datetime' => date('Y-m-d H:i:s',$unixtime)
		);
		$this->ajaxReturn($ret,'ok',1);
	}
}