<?php
namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use yii\web\Controller;
use backend\models\CompanyCategroy;
use backend\models\UserLog;
use backend\models\User;
use backend\models\AutoItemNum;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**

     * @inheritdoc
     *
     */
    public function beforeAction($action)
    {

        if ($action->id == 'login' || $action->id == 'captcha' || $action->id == 'error') {
            return true;
        }
        if (!Yii::$app->user->identity) {
            $this->redirect(['site/login']);
            return false;
        } else {
            return true;
        }
    }
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                //'backColor'=>0x000000,//背景颜色
                'maxLength' => 3, //最大显示个数
                'minLength' => 3,//最少显示个数
                //'padding' => 5,//间距
                'height'=>50,//高度
                'width' => 80,  //宽度
                // 'foreColor'=>0xffffff,     //字体颜色
                //'offset'=>4,        //设置字符偏移量 有效果
                //'controller'=>'login',        //拥有这个动作的controller
            ],
        ];
    }
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $company_categrouy_id = Yii::$app->user->identity->company_categroy_id;
        $companyCategroy = CompanyCategroy::find()
        ->where(['id'=>$company_categrouy_id])
        ->asArray()
        ->one();
        return $this->render('index',['company'=>$companyCategroy]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        //var_dump($model);exit();
        $this->layout = false;
        if (!Yii::$app->user->isGuest && \Yii::$app->user->identity->rank !== 1) {
            Yii::$app->session->setFlash("info_rank", "级别限制");
            //区分公司  区分用户权限  判断是否离职  判断试用或者正式用户
            //此处判断是否离职  已经离职直接返回消息拒绝登录
            $is_staff =  \Yii::$app->user->identity->is_staff;
            if(!$is_staff){
                Yii::$app->user->logout();
                Yii::$app->session->setFlash("info_rank", "该用户已离职！");
                return $this->render('login', [
                    'model' => $model,
                ]);
            }
           return $this->goHome();
          //  return $this->redirect(['/site/index']);
        }

        if ($model->load(Yii::$app->request->post()) &&$model->login()) {
            //查询登录人权限是否是hr  如果是hr 一线员工也需要 允许登录
            $user_id = Yii::$app->user->identity->id;
            $roleNames = \Yii::$app->authManager->getRolesByUser($user_id);
            $roleKeyName = key($roleNames);
            //$roleKeyNum == 3的时候 该登录人是hr
            $roleKeyNum = AutoItemNum::find()->select('item_num')
                ->where('item_name = :roleKeyName',[':roleKeyName'=>$roleKeyName])
                ->column();
            if(\Yii::$app->user->identity->rank !=1 || in_array(\Yii::$app->user->identity->id, \Yii::$app->params['through']) || (\Yii::$app->user->identity->rank ==1 && $roleKeyNum[0] == 3))
            {
                //区分公司  区分用户权限  判断是否离职  判断试用或者正式用户
                //此处判断是否离职  已经离职直接返回消息拒绝登录
                $is_staff =  \Yii::$app->user->identity->is_staff;
                if(!$is_staff){
                    Yii::$app->user->logout();
                    Yii::$app->session->setFlash("info_rank", "该用户已离职！");
                    return $this->render('login', [
                        'model' => $model,
                    ]);
                }
                
                $log_title = "登录后台";
                $log_text = "登录后台";
                if($this->addLog($log_title,$log_text))
                {
                   return $this->redirect(['/site/index']); 
                }
                else
                {
                    return $this->redirect(['/site/index']); 
                }
               
            }else{
                Yii::$app->user->logout();
                Yii::$app->session->setFlash("info_rank", "级别限制");
                return $this->render('login', [
                    'model' => $model,
                ]);
            }
           
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
/*    public $enableCsrfValidation = false;*/
    public function actionLogout()
    {
        return $this->redirect('/site/login');
        //return $this->goHome();
    }

    public function actionUserout()
    {
        $log_title = "退出后台";
        $log_text = "退出后台";
        $this->addLog($log_title,$log_text);
        Yii::$app->user->logout();
        return $this->redirect('/site/login');
    }

    /*登录跟踪    */
    private  function addLog($log_title,$log_text)
    {
        $userLog = new UserLog();
        $uid = \Yii::$app->user->id;
        $user = User::findOne($uid);
        $userLog->user_id = $uid;
        $userLog->type = 2;//登录
        $userLog->log_title = $log_title;
        $userLog->log_text = $log_text;
        $userLog->add_time = time();
     
        if(!$userLog->save())
        {
            $this->setError($userLog->errors);
            return false;
        }
    
        return true;
    }


}
