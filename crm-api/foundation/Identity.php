<?php
namespace app\foundation;

use app\models\User;
abstract class Identity extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $domainId;
    public $head;
    public $authKey;
    public $accessToken;
    public $refreshTime;
    public $model = null;
    public $is_staff;
    public $dimission_time;
    public $company_categroy_id;

    //abstract protected static function newIdentity($user);
    
    abstract public function updateAccessToken($accessToken);
}