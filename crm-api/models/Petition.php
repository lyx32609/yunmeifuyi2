<?php

namespace app\models;

use Yii;


class Petition extends \yii\db\ActiveRecord
{
	public static function tableName()
    {
        return '{{%petition}}';
    }
    public static function getDb()
    {
        return Yii::$app->get('dbofficial');
    }
    //关联表      get(关联表Model名)
    public function getUser(){
        //参数一 关联Model名   参数二 关联字段 不能写表.t_id 自己默认后边是本Model的表id  前边是关联表的id
        return $this->hasOne(User::className(),['id'=>'uid']);
    }
    //关联表      get(关联表Model名)
    public function getExamine(){
        //参数一 关联Model名   参数二 关联字段 不能写表.t_id 自己默认后边是本Model的表id  前边是关联表的id
        return $this->hasOne(Examine::className(),['petition_id'=>'id']);
    }
    // public function rules()
    // {
    //     return [
    //         [['problem_title', 'problem_content', 'priority', 'create_time', 'user_id', 'user_name', 'update_time', 'area', 'city', 'department', 'company_id'], 'required'],
    //         [['priority', 'create_time', 'user_id', 'problem_lock', 'update_time', 'company_id'], 'integer'],
    //         [['problem_title', 'collaboration_department'], 'string', 'max' => 200],
    //         [['problem_content'], 'string', 'max' => 2000],
    //         [['user_name', 'area', 'city', 'department'], 'string', 'max' => 100]
    //     ];
    // }
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', '标题'),
            'content' => Yii::t('app', '内容'),
            'thumb_img' => Yii::t('app', '缩略图'),
            'master_img' => Yii::t('app', '原图'),
            'file' => Yii::t('app', '附件'),
            'uid' => Yii::t('app', '用户ID'),
            'status' => Yii::t('app', '签呈状态 0：不同意 1：同意 2：待审'),
            'ids' => Yii::t('app', '审批人id'),
            'company_id' => Yii::t('app', '公司id'),
            'department_id' => Yii::t('app', '部门id'),
            'create_time' => Yii::t('app', '创建时间'),
            'pass_time' => Yii::t('app', '审核时间'),
        ];
    }
}