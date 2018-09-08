<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[AuthItemNum]].
 *
 * @see AuthItemNum
 */
class AuthItemNumQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return AuthItemNum[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AuthItemNum|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}