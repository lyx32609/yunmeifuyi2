<?php
namespace official;

use app\models\User;
class Identity extends \app\foundation\Identity
{
    public $rank;
    protected static function newIdentity($user)
    {
        $identity = new static();
        $identity->id = $user->id;
        $identity->username = $user->username;
        $identity->password = $user->password;
        $identity->domainId = $user->domain_id;
        $identity->head = $user->head;
        $identity->accessToken = $user->access_token;
        $identity->refreshTime = $user->token_createtime; 
        $identity->model = $user;
        $identity->rank=$user->rank;
        $identity->is_staff=$user->is_staff;
        $identity->dimission_time=$user->dimission_time;
        $identity->company_categroy_id=$user->company_categroy_id;
        return $identity;
    }
    
    public function updateAccessToken($accessToken)
    {
        $this->model->token_createtime = time();
        $this->model->access_token = $accessToken;
        $this->model->save();
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $user = User::find()->select(['id', 'staff_code','username', 'password','domain_id','head','rank'])->where('id=:id', [':id'=>$id])->one();    

        if ($user)
        {
            return self::newIdentity($user);
        }
    
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = User::find()->select(['id','staff_code','domain_id','rank'])->where('access_token=:token', [':token'=>$token])->one();        
        if ($user)
        {
            return self::newIdentity($user);
        }
        
        return null;
    }
    
    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user = User::find()->where('username=:username', [':username'=>$username])->one();
        
        if ($user)
        {
            return self::newIdentity($user);
        }
        
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @inheritdoc
     */
    public function getDomainId()
    {
        return $this->domainId;
    }
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }
    public function getRank()
    {
        return $this->rank;
    }
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    
    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }
}