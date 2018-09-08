<?php
namespace app\services;


use app\foundation\Service;
use app\models\UserWorkSign;
use yii\data\Pagination;

class ShowWorkTimeService extends Service
{
    /**
     * 查询上下班历史设置记录
     * @param $company_id
     * @param int $page
     * @param $pageCount
     * @return array|bool|\yii\db\ActiveRecord[]
     */
    public function showWorkTime($company_id,$page=1,$pageCount)
    {
        if(!$company_id){
            $this->setError('公司ID不能为空');
            return false;
        }
        $data = UserWorkSign::find()
            ->select('morning_to_work, morning_go_work,after_to_work , after_go_work,create_time, user_name,status')
            ->where(['company_id' => $company_id])
            ->all();
        if(!$data){
            $this->setError('公司ID不存在');
            return false;
        }
        $query = UserWorkSign::find()
            ->select('morning_to_work, morning_go_work,after_to_work , after_go_work,create_time, user_name,status')
            ->where(['company_id' => $company_id]);
        $pagination = new Pagination([
            'params'=>['page'=>$page],
            'defaultPageSize' => $pageCount?$pageCount:$query->count(),
            'totalCount' => $query->count(),
        ]);
        $result = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->orderBy('create_time desc')
            ->all();
        if(!$result) {
            $this->setError('暂无数据');
            return false;
        }
        return $result;
    }
}