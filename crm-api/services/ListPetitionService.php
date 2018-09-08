<?php
namespace app\services;

use app\foundation\Service;
use app\models\Examine;
use app\models\User;
use app\models\Petition;
use yii\data\Pagination;

class ListPetitionService extends Service
{
    public function listPetition($user_id, $page_count,$page_size)
    {
        $people = User::find()
            ->andWhere('id = :user',[':user'=>$user_id])
            ->one();
        if(!$people)
        {
            $this->setError('员工不存在!');
            return false;
        }
        $query = Petition::find()
            ->select(['title','create_time','status','id'])
            ->where('uid =:staffId',[':staffId'=>$user_id])
            ->orderBy('create_time DESC');
        //分页参数
        $pages = new Pagination([
            'params'=>['page'=>$page_count],
            'defaultPageSize' => $page_size,
            'totalCount' => $query->count(),
        ]);
        $total_page = ceil($query->count()/$page_size);

        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        foreach ($model as $key=> $value)
        {
            $model[$key]['create_time'] = date('Y-m-d H:i:s',$model[$key]['create_time']);
            if (empty($model[$key]['title'])){
                $model[$key]['title'] = '';
            }
        }

        return ['list'=>$model,'total_page'=>$total_page];;

    }
    /*
     *主管签呈列表  status 1 是发起的签呈 2是接收的签呈
     * */
    public function managePetition($status,$user_id,$page_size,$page_count)
    {
        if (!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        if (!$status) {
            $this->setError('状态不可为空');
            return false;
        }
        if (!$page_size) {
            $this->setError('每页显示条数不可为空');
            return false;
        }
        if (!$page_count) {
            $this->setError('页码不可为空');
            return false;
        }
        //发起的签呈
        if ($status == 1) {
            $post = $this->listPetition($user_id,$page_count,$page_size);
            return $post;
        }
        //接收的签呈
        if ($status == 2){
            $receive = Examine::find()
                ->joinWith(['petition'])
                ->select(['off_petition.id','off_petition.title','off_petition.create_time','off_petition.status'])
                ->andWhere('off_examine.uid =:uid',[':uid'=>$user_id])
                ->andWhere('off_examine.is_visible = 1')
                ->orderBy('off_petition.create_time DESC')
                ->asArray();
            //分页参数
            $pages = new Pagination([
                'params'=>['page'=>$page_count],
                'defaultPageSize' => $page_size,
                'totalCount' => $receive->count(),
            ]);
            $total_page = ceil($receive->count()/$page_size);
            $model = $receive->offset($pages->offset)->limit($pages->limit)->all();

            foreach ($model as $key=> $value)
            {
                $model[$key]['create_time'] = date('Y-m-d H:i:s',$model[$key]['create_time']);
                if (empty($model[$key]['title'])){
                    $model[$key]['title'] = '';
                }
                unset($model[$key]['petition']);
            }
            return ['list' =>$model,'total_page'=>$total_page];
        }

    }
}