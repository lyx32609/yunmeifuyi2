<?php

namespace common\models;

use Yii;
use common\models\Menu;
use backend\models\UserRoute;
/**
 * This is the model class for table "off_menus".
 *
 * @property string $id
 * @property string $name
 * @property string $url
 * @property string $parent_id
 * @property string $sort
 * @property string $icon_class
 * @property integer $status
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_menus';
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
            [['name', 'url', 'status'], 'required'],
            [['parent_id', 'sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 255],
            [['icon_class'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Url'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'sort' => Yii::t('app', 'Sort'),
            'icon_class' => Yii::t('app', 'Icon Class'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
    
    public static function getMenuTree()
    {
        $userid = Yii::$app->user->identity->id;
        if($userid)
        {
            $userroute = UserRoute::find()->where(['userid'=>$userid])->asArray()->one();
             if($userroute)
             {
                $menuids = explode(',',$userroute['menuids']);
            } 
        }

        $data = Menu::find()
                ->andWhere(['status'=>0])
                ->asArray()
                ->all();
        $tmp = array();
        $rows = array();
        $rank = Yii::$app->user->identity->rank;
        foreach ($data as $item)
        {
            $rows[$item['id']] = $item;
        }
        //return $rows;
        foreach ($rows as $row)
        {
            $isadmin = in_array($userid, Yii::$app->params['through']);//是否为超级管理员
            if(!$isadmin){
                if($row['isopen'] == 1)//是否对外开放
                {
                    if($row['isblock'] == 1)//是否在界面显示
                    {
                        if($row['parent_id'] && isset($rows[$row['parent_id']]))//一级菜单
                        {
                            if($row['status'] == 0)
                            {
                                $tmp[$row['parent_id']]['children'][$row['id']] = array(
                                    'name'=>$row['name'],
                                    'status'=>$row['status'],
                                    'url'=>$row['url'],
                                    'icon'=>$row['icon_class'],
                                    'block' =>$rank == 30 ? 1 : (in_array($userid, Yii::$app->params['through'])? 1 : ( isset($menuids) ?  (in_array($row['id'], $menuids ) ? 1 : 0) : 0 ) ),
                                );
                            }
                        }
                        else
                        {
                            // var_dump($row['parent_id']);
                            $tmp[$row['id']]['name'] = $row['name'];
                            $tmp[$row['id']]['status'] = $row['status'];
                            $tmp[$row['id']]['url'] = $row['url'];
                            $tmp[$row['id']]['icon_class'] = $row['icon_class'];
                            $tmp[$row['id']]['block'] =  $rank == 30 ? 1 : ( in_array($userid, Yii::$app->params['through'])? 1 : ( isset($menuids) ?  (in_array($row['id'], $menuids ) ? 1 : 0) : 0 )) ;
                        }
                    }
                }
            }
            else//不是超级管理员
            
            {
                if($row['isblock'] == 1)
                {
                    if($row['parent_id'] && isset($rows[$row['parent_id']]))//一级菜单
                    {
                        if($row['status'] == 0)
                        {
                            $tmp[$row['parent_id']]['children'][$row['id']] = array(
                                'name'=>$row['name'],
                                'status'=>$row['status'],
                                'url'=>$row['url'],
                                'icon'=>$row['icon_class'],
                                'block' =>$rank == 30 ? 1 : (in_array($userid, Yii::$app->params['through'])? 1 : ( isset($menuids) ?  (in_array($row['id'], $menuids ) ? 1 : 0) : 0 ) ),
                            );
                        }
                    }
                    else
                    {
                        $tmp[$row['id']]['name'] = $row['name'];
                        $tmp[$row['id']]['status'] = $row['status'];
                        $tmp[$row['id']]['url'] = $row['url'];
                        $tmp[$row['id']]['icon_class'] = $row['icon_class'];
                        $tmp[$row['id']]['block'] =  $rank == 30 ? 1 : ( in_array($userid, Yii::$app->params['through'])? 1 : ( isset($menuids) ?  (in_array($row['id'], $menuids ) ? 1 : 0) : 0 )) ;
                    }
                }
            }

        }
        $tree = array();
         
        foreach ($tmp as $key=>$item)
        {
            $tree[$key] = $item;
        }
        return $tree;
    }
}
