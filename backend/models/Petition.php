<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_petition".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property string $master_img
 * @property string $file
 * @property integer $uid
 * @property integer $status
 * @property string $ids
 * @property integer $company_id
 * @property integer $department_id
 * @property integer $create_time
 * @property integer $pass_time
 * @property string $is_show
 */
class Petition extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $flag;
    public static function tableName()
    {
        return 'off_petition';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbofficial');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['uid', 'status', 'company_id', 'department_id', 'create_time', 'pass_time'], 'integer'],
            [['title'], 'string', 'max' => 255],
            // [['master_img', 'file'], 'string', 'max' => 600],
            [['ids'], 'string', 'max' => 100],
            [['is_show'], 'string', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'master_img' => 'Master Img',
            'file' => 'File',
            'uid' => 'Uid',
            'status' => 'Status',
            'ids' => 'Ids',
            'company_id' => 'Company ID',
            'department_id' => 'Department ID',
            'create_time' => 'Create Time',
            'pass_time' => 'Pass Time',
            'is_show' => 'Is Show',
        ];
    }
    public static function findCompany()
    {
        $where = "";
        if(in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//超级管理员（查看所有）
        {
            $where = "";
        }
        if(Yii::$app->user->identity->rank == 30)//主公司经理(查看主公司及分公司数据)
        {
            $company = [];
            $fly = Yii::$app->user->identity->company_categroy_id;
            $child = CompanyCategroy::find()
                ->select("id")
                ->where(["fly"=>$fly])
                ->asArray()
                ->all();
            if(count($child) > 0)
            {
                foreach($child as $k=>$v)
                {
                    $company[$k] = $v['id'];
                    $company[$k+1] = $fly;
                }
            }
            else
            {
                $company[0] = $fly;
            }
            $where = ["in","id",$company];
        }
        $where_domain = "";
        $company_data = CompanyCategroy::find()
            ->select(["id","name"])
            ->where($where)
            ->andWhere($where_domain);
        return $company_data;
    }
    public static function  findDepartment($company_id)
    {
        $fly = Yii::$app->user->identity->company_categroy_id;
        //超级管理员(查看所有)
        if(in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
        {
            $where = "";
        }
        elseif(Yii::$app->user->identity->rank == 30)//主公司经理(查看主公司及分公司数据)
        {
            $child = CompanyCategroy::find()
                ->select("id")
                ->where(["fly"=>$fly])
                ->asArray()
                ->all();
            if(count($child) > 0)
            {
                foreach($child as $k=>$v)
                {
                    $company[$k] = $v['id'];
                    $company[$k+1] = $fly;
                }
            }
            else
            {
                $company[0] = $fly;
            }
            $where = ["in","id",$company];
        }
        else//其他
        {
            $where = ["company" => $fly];
        }

        if($company_id)
        {
            $where_company = ["company" => $company_id];
        }
        else
        {
            $where_company = "";
        }
        $department = UserDepartment::find()
            ->where($where)
            ->andWhere($where_domain)
            ->andWhere($where_company);
        return $department;
    }
    public function getDepartment()
    {
        return $this->hasOne(UserDepartment::findDepartment(), ['id'=>'department_id']);
    }
}
