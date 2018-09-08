<?php

namespace app\services;

use app\foundation\Service;
use app\models\Examine;
use app\models\User;
use app\models\Petition;
use yii\data\Pagination;
use app\models\Regions;

class PetitionLatestService extends Service
{
    public function listPetition($user_id, $page_count, $page_size, $flage, $type)
    {
        $people = User::find()
            ->andWhere('id = :user', [':user' => $user_id])
            ->one();
        if (!$people) {
            $this->setError('员工不存在!');
            return false;
        }
        /*
         * $flage 签呈状态  $type 签呈类型
         * $flage 状态是3（审核中）查询时status包括2和3
         * $flage 状态是4（已完成）查询时status包括0,1,4
         * */

        if ($flage || $type || $flage === '0' || $type === '0') {
            if ($type || $type === '0') {
                if ($flage || $flage === '0') {
                    if ($flage == 3) {
                        $query = Petition::find()
                            ->select(['type', 'create_time', 'status', 'id'])
                            ->where('uid =:staffId', [':staffId' => $user_id])
                            ->andWhere(['is_show' => 1])
                            ->andWhere(['type' => $type])
                            ->andWhere(['in', 'status', [2, 3]])
                            ->orderBy('create_time DESC');
                    } elseif ($flage == 4) {
                        $query = Petition::find()
                            ->select(['type', 'create_time', 'status', 'id'])
                            ->where('uid =:staffId', [':staffId' => $user_id])
                            ->andWhere(['is_show' => 1])
                            ->andWhere(['type' => $type])
                            ->andWhere(['in', 'status', [0, 1, 4]])
                            ->orderBy('create_time DESC');
                    } else {
                        $query = Petition::find()
                            ->select(['type', 'create_time', 'status', 'id'])
                            ->where('uid =:staffId', [':staffId' => $user_id])
                            ->andWhere(['is_show' => 1])
                            ->andWhere(['status' => $flage])
                            ->andWhere(['type' => $type])
                            ->orderBy('create_time DESC');
                    }


                } else {
                    $query = Petition::find()
                        ->select(['type', 'create_time', 'status', 'id'])
                        ->where('uid =:staffId', [':staffId' => $user_id])
                        ->andWhere(['is_show' => 1])
                        ->andWhere(['type' => $type])
                        ->orderBy('create_time DESC');

                }

            } else {

                if ($flage == 3) {
                    $query = Petition::find()
                        ->select(['type', 'create_time', 'status', 'id'])
                        ->where('uid =:staffId', [':staffId' => $user_id])
                        ->andWhere(['is_show' => 1])
                        ->andWhere(['in', 'status', [2, 3]])
                        ->orderBy('create_time DESC');
                }elseif ($flage == 4) {
                    $query = Petition::find()
                        ->select(['type', 'create_time', 'status', 'id'])
                        ->where('uid =:staffId', [':staffId' => $user_id])
                        ->andWhere(['is_show' => 1])
                        ->andWhere(['in', 'status', [0,1,4]])
                        ->orderBy('create_time DESC');

                }else {
                    $query = Petition::find()
                        ->select(['type', 'create_time', 'status', 'id'])
                        ->where('uid =:staffId', [':staffId' => $user_id])
                        ->andWhere(['is_show' => 1])
                        ->andWhere(['status' => $flage])
                        ->orderBy('create_time DESC');
                }
            }

        } else {

            $query = Petition::find()
                ->select(['type', 'create_time', 'status', 'id'])
                ->where('uid =:staffId', [':staffId' => $user_id])
                ->andWhere(['is_show' => 1])
                ->orderBy('create_time DESC');
        }

        //分页参数
        $pages = new Pagination([
            'params' => ['page' => $page_count],
            'defaultPageSize' => $page_size,
            'totalCount' => $query->count(),
        ]);
        $total_page = ceil($query->count() / $page_size);

        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        foreach ($model as $key => $value) {
            $model[$key]['create_time'] = date('Y-m-d H:i:s', $model[$key]['create_time']);
        }

        return ['list' => $model, 'total_page' => $total_page];

    }

    /*
     *主管签呈列表  status 1 是发起的签呈 2是接收的签呈
     * */
    public function managePetition($status, $user_id, $page_size, $page_count, $flage, $type)
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
            $post = $this->listPetition($user_id, $page_count, $page_size, $flage, $type);
            return $post;
        }
        //接收的签呈
        if ($status == 2) {
            /*
             * $flage 签呈状态  $type 签呈类型
             * $flage 状态是3（审核中）查询时status包括2和3
             * $flage 状态是4（已完成）查询时status包括0,1,4
             * */

            if ($flage || $type || $flage === '0' || $type === '0') {
                if ($type || $type === '0') {
                    if ($flage || $flage === '0') {
                        //flage为3时审核中
                        if ($flage == 3) {
                            $receive = Examine::find()
                                ->joinWith(['petition'])
                                ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status'])
                                ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                                ->andWhere('off_examine.is_visible = 1')
                                ->andWhere(['off_petition.is_show' => 1])
                                ->andWhere(['off_petition.type' => $type])
                                ->andWhere(['in', 'off_petition.status', [2, 3]])
                                ->orderBy('off_petition.create_time DESC')
                                ->asArray();
                        } //flage 状态是4（已完成）
                        elseif ($flage == 4) {
                            $receive = Examine::find()
                                ->joinWith(['petition'])
                                ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status'])
                                ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                                ->andWhere('off_examine.is_visible = 1')
                                ->andWhere(['off_petition.is_show' => 1])
                                ->andWhere(['off_petition.type' => $type])
                                ->andWhere(['in', 'off_petition.status', [0, 1, 4]])
                                ->orderBy('off_petition.create_time DESC')
                                ->asArray();
                        } else {
                            $receive = Examine::find()
                                ->joinWith(['petition'])
                                ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status'])
                                ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                                ->andWhere('off_examine.is_visible = 1')
                                ->andWhere(['off_petition.is_show' => 1])
                                ->andWhere(['off_petition.type' => $type])
                                ->andWhere(['off_petition.status' => $flage])
                                ->orderBy('off_petition.create_time DESC')
                                ->asArray();
                        }


                    } else {
                        //off_petition.type 为66的是之前版本提交的签呈，首版签呈没有type字段，添加该字段后将之前的签呈类型改为66
                        if ($type === '0') {
                            $receive = Examine::find()
                                ->joinWith(['petition'])
                                ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status'])
                                ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                                ->andWhere('off_examine.is_visible = 1')
                                ->andWhere(['off_petition.is_show' => 1])
                                ->andWhere(['off_petition.type' => [0, 66]])
                                ->orderBy('off_petition.create_time DESC')
                                ->asArray();
                        } else {
                            $receive = Examine::find()
                                ->joinWith(['petition'])
                                ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status'])
                                ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                                ->andWhere('off_examine.is_visible = 1')
                                ->andWhere(['off_petition.is_show' => 1])
                                ->andWhere(['off_petition.type' => $type])
                                ->orderBy('off_petition.create_time DESC')
                                ->asArray();
                        }

                    }
                } else {
                    //flage为3时审核中
                    if ($flage == 3) {
                        $receive = Examine::find()
                            ->joinWith(['petition'])
                            ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status'])
                            ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                            ->andWhere('off_examine.is_visible = 1')
                            ->andWhere(['off_petition.is_show' => 1])
                            ->andWhere(['in', 'off_petition.status', [2, 3]])
                            ->orderBy('off_petition.create_time DESC')
                            ->asArray();
                    } //flage 状态是4（已完成）
                    elseif ($flage == 4) {
                        $receive = Examine::find()
                            ->joinWith(['petition'])
                            ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status'])
                            ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                            ->andWhere('off_examine.is_visible = 1')
                            ->andWhere(['off_petition.is_show' => 1])
                            ->andWhere(['in', 'off_petition.status', [0, 1, 4]])
                            ->orderBy('off_petition.create_time DESC')
                            ->asArray();
                    } else {
                        $receive = Examine::find()
                            ->joinWith(['petition'])
                            ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status'])
                            ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                            ->andWhere('off_examine.is_visible = 1')
                            ->andWhere(['off_petition.is_show' => 1])
                            ->andWhere(['off_petition.status' => $flage])
                            ->orderBy('off_petition.create_time DESC')
                            ->asArray();
                    }

                }

            } else {
                $receive = Examine::find()
                    ->joinWith(['petition'])
                    ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status'])
                    ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                    ->andWhere('off_examine.is_visible = 1')
                    ->andWhere(['off_petition.is_show' => 1])
                    ->orderBy('off_petition.create_time DESC')
                    ->asArray();
            }

            //分页参数
            $pages = new Pagination([
                'params' => ['page' => $page_count],
                'defaultPageSize' => $page_size,
                'totalCount' => $receive->count(),
            ]);
            $total_page = ceil($receive->count() / $page_size);
            $model = $receive->offset($pages->offset)->limit($pages->limit)->all();

            foreach ($model as $key => $value) {
                $model[$key]['create_time'] = date('Y-m-d H:i:s', $model[$key]['create_time']);
                unset($model[$key]['petition']);
            }
            return ['list' => $model, 'total_page' => $total_page];
        }

    }




/*  新 返回用户名称和  签呈信息*/
    public function listPetitionNew($user_id, $page_count, $page_size, $flage, $type)
    {
        $people = User::find()
            ->andWhere('id = :user', [':user' => $user_id])
            ->one();
        if (!$people) {
            $this->setError('员工不存在!');
            return false;
        }
        /*
         * $flage 签呈状态  $type 签呈类型
         * $flage 状态是3（审核中）查询时status包括2和3
         * $flage 状态是4（已完成）查询时status包括0,1,4
         * */

        if ($flage || $type || $flage === '0' || $type === '0') {
            if ($type || $type === '0') {
                if ($flage || $flage === '0') {
                    if ($flage == 3) {
                        $query = Petition::find()
                            ->select(['type', 'create_time', 'status', 'id','message'])
                            ->where('uid =:staffId', [':staffId' => $user_id])
                            ->andWhere(['is_show' => 1])
                            ->andWhere(['type' => $type])
                            ->andWhere(['in', 'status', [2, 3]])
                            ->orderBy('create_time DESC');
                    } elseif ($flage == 4) {
                        $query = Petition::find()
                            ->select(['type', 'create_time', 'status', 'id','message'])
                            ->where('uid =:staffId', [':staffId' => $user_id])
                            ->andWhere(['is_show' => 1])
                            ->andWhere(['type' => $type])
                            ->andWhere(['in', 'status', [0, 1, 4]])
                            ->orderBy('create_time DESC');
                    } else {
                        $query = Petition::find()
                            ->select(['type', 'create_time', 'status', 'id','message'])
                            ->where('uid =:staffId', [':staffId' => $user_id])
                            ->andWhere(['is_show' => 1])
                            ->andWhere(['status' => $flage])
                            ->andWhere(['type' => $type])
                            ->orderBy('create_time DESC');
                    }


                } else {
                    $query = Petition::find()
                        ->select(['type', 'create_time', 'status', 'id','message'])
                        ->where('uid =:staffId', [':staffId' => $user_id])
                        ->andWhere(['is_show' => 1])
                        ->andWhere(['type' => $type])
                        ->orderBy('create_time DESC');

                }

            } else {

                if ($flage == 3) {
                    $query = Petition::find()
                        ->select(['type', 'create_time', 'status', 'id','message'])
                        ->where('uid =:staffId', [':staffId' => $user_id])
                        ->andWhere(['is_show' => 1])
                        ->andWhere(['in', 'status', [2, 3]])
                        ->orderBy('create_time DESC');
                }elseif ($flage == 4) {
                    $query = Petition::find()
                        ->select(['type', 'create_time', 'status', 'id','message'])
                        ->where('uid =:staffId', [':staffId' => $user_id])
                        ->andWhere(['is_show' => 1])
                        ->andWhere(['in', 'status', [0,1,4]])
                        ->orderBy('create_time DESC');

                }else {
                    $query = Petition::find()
                        ->select(['type', 'create_time', 'status', 'id','message'])
                        ->where('uid =:staffId', [':staffId' => $user_id])
                        ->andWhere(['is_show' => 1])
                        ->andWhere(['status' => $flage])
                        ->orderBy('create_time DESC');
                }
            }

        } else {

            $query = Petition::find()
                ->select(['type', 'create_time', 'status', 'id','message'])
                ->where('uid =:staffId', [':staffId' => $user_id])
                ->andWhere(['is_show' => 1])
                ->orderBy('create_time DESC');
        }

        //分页参数
        $pages = new Pagination([
            'params' => ['page' => $page_count],
            'defaultPageSize' => $page_size,
            'totalCount' => $query->count(),
        ]);
        $total_page = ceil($query->count() / $page_size);

        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        foreach ($model as $key => $value) {
            if ($model[$key]['create_time'] < '1527609600'){
                $model[$key]['create_time'] = date('Y-m-d', $model[$key]['create_time']);
                $model[$key]['message'] = '';
            }else{
                $model[$key]['create_time'] = date('Y-m-d', $model[$key]['create_time']);
                $message = json_decode($model[$key]['message'],true);
                if($model[$key]['type'] == 0){
                    $model[$key]['message'] = $message['title'];
                }elseif ($model[$key]['type'] == 1){
                    $model[$key]['message'] = $message['reason'];
                }elseif ($model[$key]['type'] == 2){
                    $model[$key]['message'] = $message['carmodel'];
                }elseif ($model[$key]['type'] == 3){
                    $model[$key]['message'] = $message['purpose'];
                }elseif ($model[$key]['type'] == 4){
                    $model[$key]['message'] = $message['title'];
                }elseif ($model[$key]['type'] == 5){
                    $model[$key]['message'] = $message['goods'];
                }elseif ($model[$key]['type'] == 6){
                    $model[$key]['message'] = $message['name'];
                }elseif ($model[$key]['type'] == 7){
                    $model[$key]['message'] = $message['title'];
                }elseif ($model[$key]['type'] == 8){
                    $model[$key]['message'] = $message['site'];
                }elseif ($model[$key]['type'] == 9){
                    $model[$key]['message'] = $message['duration'];
                }elseif ($model[$key]['type'] == 10){
                    $model[$key]['message'] = $message['site'];
                }elseif ($model[$key]['type'] == 11){
                    $model[$key]['message'] = $message['entrydate'];
                }elseif ($model[$key]['type'] == 12){
                    $model[$key]['message'] = $message['entrydate'];
                }elseif ($model[$key]['type'] == 13){
                    $model[$key]['message'] = $message['type'];
                }elseif ($model[$key]['type'] == 14){
                    $model[$key]['message'] = $message['demanddepartment'];
                }
            }
        }
        return ['list' => $model, 'total_page' => $total_page];

    }
    public function managePetitionNew($status, $user_id, $page_size, $page_count, $flage, $type)
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
            $post = $this->listPetitionNew($user_id, $page_count, $page_size, $flage, $type);
            return $post;
        }
        //接收的签呈
        if ($status == 2) {
            /*
             * $flage 签呈状态  $type 签呈类型
             * $flage 状态是3（审核中）查询时status包括2和3
             * $flage 状态是4（已完成）查询时status包括0,1,4
             * */

            if ($flage || $type || $flage === '0' || $type === '0') {
                if ($type || $type === '0') {
                    if ($flage || $flage === '0') {
                        //flage为3时审核中
                        if ($flage == 3) {
                            $receive = Examine::find()
                                ->joinWith(['petition'])
                                ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status','off_petition.uid','off_petition.message'])
                                ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                                ->andWhere('off_examine.is_visible = 1')
                                ->andWhere(['off_petition.is_show' => 1])
                                ->andWhere(['off_petition.type' => $type])
                                ->andWhere(['in', 'off_petition.status', [2, 3]])
                                ->orderBy('off_petition.create_time DESC')
                                ->asArray();
                        } //flage 状态是4（已完成）
                        elseif ($flage == 4) {
                            $receive = Examine::find()
                                ->joinWith(['petition'])
                                ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status','off_petition.uid','off_petition.message'])
                                ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                                ->andWhere('off_examine.is_visible = 1')
                                ->andWhere(['off_petition.is_show' => 1])
                                ->andWhere(['off_petition.type' => $type])
                                ->andWhere(['in', 'off_petition.status', [0, 1, 4]])
                                ->orderBy('off_petition.create_time DESC')
                                ->asArray();
                        } else {
                            $receive = Examine::find()
                                ->joinWith(['petition'])
                                ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status','off_petition.uid','off_petition.message'])
                                ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                                ->andWhere('off_examine.is_visible = 1')
                                ->andWhere(['off_petition.is_show' => 1])
                                ->andWhere(['off_petition.type' => $type])
                                ->andWhere(['off_petition.status' => $flage])
                                ->orderBy('off_petition.create_time DESC')
                                ->asArray();
                        }


                    } else {
                        //off_petition.type 为66的是之前版本提交的签呈，首版签呈没有type字段，添加该字段后将之前的签呈类型改为66
                        if ($type === '0') {
                            $receive = Examine::find()
                                ->joinWith(['petition'])
                                ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status','off_petition.uid','off_petition.message'])
                                ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                                ->andWhere('off_examine.is_visible = 1')
                                ->andWhere(['off_petition.is_show' => 1])
                                ->andWhere(['off_petition.type' => [0, 66]])
                                ->orderBy('off_petition.create_time DESC')
                                ->asArray();
                        } else {
                            $receive = Examine::find()
                                ->joinWith(['petition'])
                                ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status','off_petition.uid','off_petition.message'])
                                ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                                ->andWhere('off_examine.is_visible = 1')
                                ->andWhere(['off_petition.is_show' => 1])
                                ->andWhere(['off_petition.type' => $type])
                                ->orderBy('off_petition.create_time DESC')
                                ->asArray();
                        }

                    }
                } else {
                    //flage为3时审核中
                    if ($flage == 3) {
                        $receive = Examine::find()
                            ->joinWith(['petition'])
                            ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status','off_petition.uid','off_petition.message'])
                            ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                            ->andWhere('off_examine.is_visible = 1')
                            ->andWhere(['off_petition.is_show' => 1])
                            ->andWhere(['in', 'off_petition.status', [2, 3]])
                            ->orderBy('off_petition.create_time DESC')
                            ->asArray();
                    } //flage 状态是4（已完成）
                    elseif ($flage == 4) {
                        $receive = Examine::find()
                            ->joinWith(['petition'])
                            ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status','off_petition.uid','off_petition.message'])
                            ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                            ->andWhere('off_examine.is_visible = 1')
                            ->andWhere(['off_petition.is_show' => 1])
                            ->andWhere(['in', 'off_petition.status', [0, 1, 4]])
                            ->orderBy('off_petition.create_time DESC')
                            ->asArray();
                    } else {
                        $receive = Examine::find()
                            ->joinWith(['petition'])
                            ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status','off_petition.uid','off_petition.message'])
                            ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                            ->andWhere('off_examine.is_visible = 1')
                            ->andWhere(['off_petition.is_show' => 1])
                            ->andWhere(['off_petition.status' => $flage])
                            ->orderBy('off_petition.create_time DESC')
                            ->asArray();
                    }

                }

            } else {
                $receive = Examine::find()
                    ->joinWith(['petition'])
                    ->select(['off_petition.id', 'off_petition.type', 'off_petition.create_time', 'off_petition.status','off_petition.uid','off_petition.message'])
                    ->andWhere('off_examine.uid =:uid', [':uid' => $user_id])
                    ->andWhere('off_examine.is_visible = 1')
                    ->andWhere(['off_petition.is_show' => 1])
                    ->orderBy('off_petition.create_time DESC')
                    ->asArray();
            }

            //分页参数
            $pages = new Pagination([
                'params' => ['page' => $page_count],
                'defaultPageSize' => $page_size,
                'totalCount' => $receive->count(),
            ]);
            $total_page = ceil($receive->count() / $page_size);
            $model = $receive->offset($pages->offset)->limit($pages->limit)->all();

            foreach ($model as $key => $value) {
                if ($model[$key]['create_time'] < '1527609600'){
                    $model[$key]['create_time'] = date('Y-m-d', $model[$key]['create_time']);
                    $model[$key]['uid'] = '';
                    $model[$key]['message'] = '';
                }else{
                    $model[$key]['create_time'] = date('Y-m-d', $model[$key]['create_time']);
                    $model[$key]['uid'] = User::find()->select('name')->where(['id'=>$model[$key]['uid']])->one()->name;
                    $message = json_decode($model[$key]['message'],true);
                    if($model[$key]['type'] == 0){
                        $model[$key]['message'] = $message['title'];
                    }elseif ($model[$key]['type'] == 1){
                        $model[$key]['message'] = $message['reason'];
                    }elseif ($model[$key]['type'] == 2){
                        $model[$key]['message'] = $message['carmodel'];
                    }elseif ($model[$key]['type'] == 3){
                        $model[$key]['message'] = $message['purpose'];
                    }elseif ($model[$key]['type'] == 4){
                        $model[$key]['message'] = $message['title'];
                    }elseif ($model[$key]['type'] == 5){
                        $model[$key]['message'] = $message['goods'];
                    }elseif ($model[$key]['type'] == 6){
                        $model[$key]['message'] = $message['name'];
                    }elseif ($model[$key]['type'] == 7){
                        $model[$key]['message'] = $message['title'];
                    }elseif ($model[$key]['type'] == 8){
                        $model[$key]['message'] = $message['site'];
                    }elseif ($model[$key]['type'] == 9){
                        $model[$key]['message'] = $message['duration'];
                    }elseif ($model[$key]['type'] == 10){
                        $model[$key]['message'] = $message['site'];
                    }elseif ($model[$key]['type'] == 11){
                        $model[$key]['message'] = $message['entrydate'];
                    }elseif ($model[$key]['type'] == 12){
                        $model[$key]['message'] = $message['entrydate'];
                    }elseif ($model[$key]['type'] == 13){
                        $model[$key]['message'] = $message['type'];
                    }elseif ($model[$key]['type'] == 14){
                        $model[$key]['message'] = $message['demanddepartment'];
                    }
                }
                unset($model[$key]['petition']);
            }
            return ['list' => $model, 'total_page' => $total_page];
        }

    }
    /*  新 返回用户名称和  签呈信息*/
}