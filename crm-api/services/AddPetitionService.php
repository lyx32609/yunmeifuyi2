<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\foundation\Identity;
use app\models\Petition;
use app\models\Examine;
use app\models\UserDomain;
use app\models\UserDepartment;
use app\models\ProviceCity;

class AddPetitionService extends Service
{
	public function addPetition($user_id, $title,$content,$master_img,$file,$ids)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		if(!$title) {
			$this->setError('标题不可为空');
			return false;
		}
		if(!$content) {
			$this->setError('内容不可为空');
			return false;
		}
		if(!$ids) {
            $this->setError('审批人不可为空');
            return false;
        }
		$user = User::find()
				->select(['name', 'department_id', 'company_categroy_id'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		$result = new Petition;
		$result->uid = $user_id;
		$result->title = $title;
		$result->content = $content;
		$result->master_img = $master_img;
		$result->file = $file;
		$result->company_id = $user['company_categroy_id'];
		$result->department_id = $user['department_id'];
		$result->create_time = time();
		$result->status = 2;
		$result->ids = $ids;
		if($result->save()) {
			$pass_id = explode(',', $ids);
			foreach ($pass_id as $key => $value) {
				$examine = new Examine;
				$examine->petition_id = $result->id;
				if ($key == 0){
                    $examine->is_visible = 1;
                }else{
                    $examine->is_visible = 2;
                }
				$examine->uid = $value;
				$examine->status = 2;
				$res = $examine->save();
			}
			if ($res) {
				return '签呈提交成功';
			}
		}else{
			$this->setError('签呈提交失败');
			return false;
		}
	}
	
    /**
     * @param $user_id  提报人id
     * @param $master_img 图片
     * @param $file 附件
     * @param $ids  审批人
     * @param $type 签呈类型15种
     * 0通用  1领用  2用车  3付款  4报销  5采购  6用证  7用印  8出差  9加班  10外出  11转正  12离职  13请假  14招聘
     * @param $message  签呈的信息
     * @return bool|string
     */
    public function addPetitionNew($user_id,$master_img,$file,$ids,$type,$message)
    {
        if(!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        $user = User::find()
            ->select(['name', 'department_id', 'company_categroy_id'])
            ->where(['id' => $user_id])
            ->asArray()
            ->one();
        $result = new Petition;
        $result->uid = $user_id;
        $result->message = $message;
        $result->master_img = $master_img;
        $result->file = $file;
        $result->type = $type;
        $result->company_id = $user['company_categroy_id'];
        $result->department_id = $user['department_id'];
        $result->create_time = time();
        $result->status = 2;
        $result->ids = $ids;
        if($result->save()) {
            $pass_id = explode(',', $ids);
            foreach ($pass_id as $key => $value) {
                $examine = new Examine;
                $examine->petition_id = $result->id;
                if ($key == 0){
                    $examine->is_visible = 1;
                }else{
                    $examine->is_visible = 2;
                }
                $examine->uid = $value;
                $examine->status = 2;
                $res = $examine->save();
            }
            if ($res) {
                return '签呈提交成功';
            }
        }else{
            $this->setError('签呈提交失败');
            return false;
        }
    }
}
