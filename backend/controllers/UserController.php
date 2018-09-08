<?php

namespace backend\controllers;

use Yii;
use backend\models\User;
use backend\models\UserSearch;
use backend\models\Menus;
use backend\models\UserRoute;
use backend\models\Assignment;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\UserGroup;
use backend\models\UserDepartment;
use backend\models\Regions;
use backend\models\CompanyCategroy;
use backend\models\AuthAssignment;
use yii\data\ActiveDataProvider;
use backend\models\UserLog;
use backend\models\PutImei;

require(__DIR__ . '/../../vendor/excel/PHPExcel.php');


/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{
    public $userClassName;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST', 'Get'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->get('select'))//查询
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('UserSearch');
            } else {
                $data = \Yii::$app->request->get('UserSearch');
            }
//            var_dump($data);die;
            $username = $data["username"];
            $name = $data["name"];
            $phone = $data["phone"];
            $rank = empty($data["rank"]) ? "" : $data["rank"];
            $item_name = empty($data["item_name"]) ? "" : $data["item_name"];
            if ((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//总经理或者超级管理员
            {
                $area = $data['area'];
                $city = $data['city'];
                $company_id = $data['company_categroy_id'];
            }
            $department = empty($data['department']) ? "" : $data['department'];
            $searchModel = new UserSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->orderBy('setting.id desc');
            $companyid = Yii::$app->user->identity->company_categroy_id;
            $where_company = "";
            if (empty($company_id))//没有选公司
            {
                if (!in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                    if ($rank == 30)//主公司经理
                    {
                        $child = CompanyCategroy::find()
                            ->select(["id"])
                            ->where(["fly" => $companyid])
                            ->asArray()
                            ->all();
                        $count = count($child);
                        if ($count > 0) {
                            foreach ($child as $k => $v) {
                                $company[$k] = $v['id'];
                                $company[$k + 1] = $companyid;
                            }
                        } else {
                            $company[0] = $companyid;
                        }
                        $where_company = ['in', "company_categroy_id", $company];
                    }
                    if (Yii::$app->user->identity->rank == 3)//子公司经理
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    }
                    $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
                    if ((Yii::$app->user->identity->rank == 4) && in_array("hr", $rules))//部门经理同时是hr
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    } elseif ((Yii::$app->user->identity->rank == 4) && !in_array("hr", $rules))//部门经理非hr
                    {
                        $where_company = ["department_id" => Yii::$app->user->identity->department_id];
                    }
                } else {
                    $where_company = "";
                }
            } else {
                $where_company = ["company_categroy_id" => $company_id];
            }
            $dataProvider->query->andWhere($where_company);
            if (!empty($city)) {
                $dataProvider->query->andWhere(['domain_id' => $city]);
            }
            if (!empty($company_id) && $company_id != '请选择公司') {
                $dataProvider->query->andWhere(['company_categroy_id' =>  $company_id]);
            }
            if (!empty($department) && $department != '请选择部门') {
                $dataProvider->query->andWhere(['department_id' => $department]);                   //原部门
                /*
                 * 添加所属部门（多个）选择条件
                 * */
                $sql = "select username,id,include_department_id from off_user where FIND_IN_SET($department,include_department_id)";
                $res = Yii::$app->dbofficial->createCommand($sql)->queryAll();
                if($res){
                    $user_ids = array_column($res,'id');
                    $dataProvider->query->orWhere(['in','id',$user_ids]);
                }
            }
            if (!empty($username)) {

                $user = User::findOne(['username' => $username]);
                if ($user) {
                    $dataProvider->query->andWhere(['username' => $username]);
                } else {
                    echo '<script>alert("用户不存在");history.back()</script>';
                }
            }
            if (!empty($name)) {
                $userdata = User::findOne(['name' => $name]);
                if ($userdata) {
                    $dataProvider->query->andWhere(['name' => $name]);
                } else {
                    echo '<script>alert("用户不存在");history.back()</script>';
                }
            }
            if (!empty($phone)) {
                $dataProvider->query->andWhere(['phone' => $phone]);
            }
            if (!empty($rank)) {
                $dataProvider->query->andWhere(['rank' => $rank]);
            }
            if (!empty($item_name)) {
                $dataProvider->query->andWhere(['item_name' => $item_name]);
            }
            $dataProvider->query->orderBy("id desc");
            $searchModel->username = $username;
            $searchModel->name = $name;
            $searchModel->phone = $phone;
            $searchModel->rank = $rank;
            $searchModel->item_name = $item_name;
            if ((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                $searchModel->area = $area;
                $searchModel->city = $city;
                $searchModel->company_categroy_id = $company_id;
            }
            $searchModel->department_id = $department;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } elseif (Yii::$app->request->get('export'))//导出
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('UserSearch');
            } else {
                $data = \Yii::$app->request->get('UserSearch');
            }
            $username = $data["username"];
            $name = $data["name"];
            $phone = $data["phone"];
            $rank = empty($data["rank"]) ? "" : $data["rank"];
            $item_name = empty($data["item_name"]) ? "" : $data["item_name"];
            if ((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//总经理或者超级管理员
            {
                $area = $data['area'];
                $city = $data['city'];
                $company_id = $data['company_categroy_id'];
            }
            $department = empty($data['department']) ? "" : $data['department'];
            $companyid = Yii::$app->user->identity->company_categroy_id;
            $where_company = "";
            if (empty($company_id))//没有选公司
            {
                if (!in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                    if (Yii::$app->user->identity->rank == 30)//主公司经理
                    {
                        $child = CompanyCategroy::find()
                            ->select(["id"])
                            ->where(["fly" => $companyid])
                            ->asArray()
                            ->all();
                        $count = count($child);
                        if ($count > 0) {
                            foreach ($child as $k => $v) {
                                $company[$k] = $v['id'];
                                $company[$k + 1] = $companyid;
                            }
                        } else {
                            $company[0] = $companyid;
                        }
                        $where_company = ['in', "company_categroy_id", $company];
                    }
                    if (Yii::$app->user->identity->rank == 3)//子公司经理
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    }
                    $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
                    if ((Yii::$app->user->identity->rank == 4) && in_array("hr", $rules))//部门经理同时是hr
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    } elseif ((Yii::$app->user->identity->rank == 4) && !in_array("hr", $rules))//部门经理非hr
                    {
                        $where_company = ["department_id" => Yii::$app->user->identity->department_id];
                    }
                } else {
                    $where_company = "";
                }
            } else {
                $where_company = ["company_categroy_id" => $company_id];
            }
            $userid = User::find()
                ->select('id')
                ->where($where_company)
                ->asArray()
                ->column();
            $model = User::find()
                ->select(['off_user.*', 'auth_assignment.item_name',])
                ->leftJoin(['auth_assignment' => AuthAssignment::tableName()], 'auth_assignment.user_id = off_user.id')
                ->where(["in", "id", $userid]);
            if (!empty($city)) {
                $model->andWhere(['domain_id' => $city]);
            }
            if (!empty($company_id) && $company_id != '请选择公司') {
                $model->andWhere(['company_categroy_id' => $company_id]);
            }
            if (!empty($department) && $department != '请选择部门') {
                $model->andWhere(['department_id' => $department]);
                /*
                 * 添加所属部门（多个）选择条件
                 * */
                $sql = "select username,id,include_department_id from off_user where FIND_IN_SET($department,include_department_id)";
                $res = Yii::$app->dbofficial->createCommand($sql)->queryAll();
                if($res){
                    $user_ids = array_column($res,'id');
                    $model->orWhere(['in','id',$user_ids]);
                }

            }
            if (!empty($username)) {
                $user = User::findOne(['username' => $username]);
                if ($user) {
                    $model->andWhere(['username' => $username]);
                } else {
                    echo '<script>alert("用户不存在");history.back()</script>';
                }
            }
            if (!empty($name)) {
                $userdata = User::findOne(['name' => $name]);
                if ($userdata) {
                    $model->andWhere(['name' => $name]);
                } else {
                    echo '<script>alert("用户不存在");history.back()</script>';
                }
            }
            if (!empty($phone)) {
                $model->andWhere(['phone' => $phone]);
            }
            if (!empty($rank)) {
                $model->andWhere(['rank' => $rank]);
            }
            if (!empty($item_name)) {
                $model->andWhere(['item_name' => $item_name]);
            }
            $model = $model->orderBy("id desc")->asArray()->all();

            error_reporting(E_ALL);
            date_default_timezone_set('Asia/ShangHai');
            $objPHPExcel = new \PHPExcel();
            /* 以下是一些设置 ，什么作者  标题啊之类的 */
            $objPHPExcel->getProperties()->setCreator(Yii::$app->user->Identity->username)
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2003 XLS Test Document")
                ->setSubject("Office 2003 XLS Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2003 openxml php")
                ->setCategory("Test result file");
            $i = 1;

            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, isset($user) ? $user->username . '(' . $user->name . ')人员信息' : '人员信息列表');
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );
            //列头
            $i = 2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, '用户名')
                ->setCellValue('B' . $i, '姓名')
                ->setCellValue('C' . $i, '联系电话')
                ->setCellValue('D' . $i, '职务级别')
                ->setCellValue('E' . $i, '权限');
            //循环获取数据
            $i = 3;

            foreach ($model as $v) {
                if ($v['rank'] == 30) {
                    $rank_name = "主公司经理";
                }
                if ($v['rank'] == 3) {
                    $rank_name = "子公司经理";
                }
                if ($v['rank'] == 4) {
                    $rank_name = "部门经理";
                }
                if ($v['rank'] == 1) {
                    if ($v["item_name"] == "deliver") {
                        $rank_name = "配送人员";
                    } else {
                        $rank_name = "一线员工";
                    }

                }
                $item = "";
                if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                    if ($v['item_name'] == "admin") {
                        $item = "后台";
                    }
                    if ($v['item_name'] == "hr") {
                        $item = "人资";
                    }
                    if ($v['item_name'] == "deliver") {
                        $item = "配送";
                    }
                    if ($v['item_name'] == "staff") {
                        $item = "员工";
                    }
                } else {
                    $item = "暂无权限查看";
                }

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $v['username'])
                    ->setCellValue('B' . $i, $v['name'])
                    ->setCellValue('C' . $i, $v['phone'])
                    ->setCellValue('D' . $i, $rank_name)
                    ->setCellValue('E' . $i, $item);
                $i++;
            }
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $i . ':E' . $i);
            $objPHPExcel->setActiveSheetIndex(0);


            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $filename = isset($user) ? $user->username . '(' . $user->name . ')人员信息' : '人员信息列表';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
            header('Cache-Control: max-age=0');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();

        }
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $companyid = Yii::$app->user->identity->company_categroy_id;
        $where_company = "";
        if (!in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
            if (Yii::$app->user->identity->rank == 30)//主公司经理
            {
                $child = CompanyCategroy::find()
                    ->select(["id"])
                    ->where(["fly" => $companyid])
                    ->asArray()
                    ->all();
                $count = count($child);
                if ($count > 0) {
                    foreach ($child as $k => $v) {
                        $company[$k] = $v['id'];
                        $company[$k + 1] = $companyid;
                    }
                } else {
                    $company[0] = $companyid;
                }
                $where_company = ['in', "company_categroy_id", $company];
            }
            if (Yii::$app->user->identity->rank == 3)//子公司或者部门经理
            {
                $where_company = ["company_categroy_id" => $companyid];
            }
            $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
            if ((Yii::$app->user->identity->rank == 4) && in_array("hr", $rules))//部门经理同时是hr
            {
                $where_company = ["company_categroy_id" => $companyid];
            } elseif ((Yii::$app->user->identity->rank == 4) && !in_array("hr", $rules))//部门经理非hr
            {
                $where_company = ["department_id" => Yii::$app->user->identity->department_id];
            }

        } else {
            $where_company = "";
        }
        $userid = User::find()
            ->select('id')
            ->where($where_company)
            ->asArray()
            ->column();
        $dataProvider->query->where(["in", "id", $userid])
            ->orderBy("id desc");
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $userid = Yii::$app->user->identity->id;
        $rank = Yii::$app->user->identity->rank;
        $companyid = Yii::$app->user->identity->company_categroy_id;
        $isadmin = in_array($userid, Yii::$app->params['through']);
        $user = User::find()->select('company_categroy_id')->where(['id' => $id])->asArray()->one();
        if ($user['company_categroy_id'] != $companyid && !$isadmin) {
            echo '<script>alert("用户不存在");history.back()</script>';
            return;
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        if($model->load(Yii::$app->request->post()) )
        {
            $group_id = Yii::$app->request->post('User')['group_id'];
            if(empty($group_id))
            {
                $group_id = 0;
            }
            $username = Yii::$app->request->post('User')['username'];
            if(!is_numeric($username))
            {
                Yii::$app->session->setFlash('info','用户名错误');
                return '<script>history.back()</script>';
            }

            $phone = Yii::$app->request->post('User')['phone'];
            if(!empty($phone) && !preg_match("/^1[34578]{1}\d{9}$/",$phone))
            {
                Yii::$app->session->setFlash('info','手机号格式错误');
                return '<script>history.back()</script>';
            }

            $name = User::findOne(['username'=>$username]);
            if($name)
            {
                Yii::$app->session->setFlash('info','用户名已存在');
                return '<script>history.back()</script>';
            }
            else
            {
                $model->head = '';
                $model->password = md5($model->password);
                $model->group_id = $group_id;
                $model->domain_id = Yii::$app->request->post('User')['domain_id'];
                $model->company_categroy_id = Yii::$app->request->post('User')['company_categroy_id'];
                $model->department_id = Yii::$app->request->post('User')['department_id'];
                $model->create_time = time();
                if($model->save())
                {
                    /*配置人员菜单权限s*/
                    if(isset(Yii::$app->request->post('User')['menuids']) && Yii::$app->request->post('User')['menuids'] != '')
                    {
                        $menuid = Yii::$app->request->post('User')['menuids'];
                        $menuids = implode(',', $menuid);
                        $UserRoute = new UserRoute();
                        $UserRoute->menuids = $menuids;
                        $UserRoute->userid = $model->id;
                        $UserRoute->save();
                    }
                    /*配置人员菜单权限e*/
                    /*操作跟踪s*/
                    $log_title = "新增用户".$username;
                    $log_text = json_encode(Yii::$app->request->post('User'));
                    $this->addLog($log_title,$log_text);
                    /*操作跟踪e*/
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                else
                {
                    Yii::$app->session->setFlash('info','新建失败');
                    return '<script>history.back()</script>';
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $old_password = $model->password;
        if ($model->load(Yii::$app->request->post())) {
            //判断是否为系统总管理    只有系统总管理能修改总经理的基本信息
            $rank = Yii::$app->user->identity->rank;
            $aid = Yii::$app->user->identity->id;
            if ($rank != 30 && !in_array($aid, Yii::$app->params['through'])) {
                $user = User::find()->where(['id' => $id])->asArray()->one();
                if ($user['rank'] == 30) {
                    Yii::$app->session->setFlash('info', '修改失败,权限不足');
                    return '<script>history.back()</script>';
                }
            }

            /*配置人员菜单权限s*/
            if (isset(Yii::$app->request->post('User')['menuids']) && Yii::$app->request->post('User')['menuids'] != '') {
                $menuid = Yii::$app->request->post('User')['menuids'];
                $menuids = implode(',', $menuid);
                $userData = UserRoute::findOne(['userid' => $id]);
                if ($userData) {
                    $userData->menuids = $menuids;
                    $userData->save();
                } else {
                    $UserRoute = new UserRoute();
                    $UserRoute->userid = $id;
                    $UserRoute->menuids = $menuids;
                    $UserRoute->save();
                }
            }
            /*配置人员菜单权限e*/

            $phone = Yii::$app->request->post('User')['phone'];
            $group_id = Yii::$app->request->post('User')['group_id'];
            if (empty($group_id)) {
                $model->group_id = 0;
            }
            if (!empty($phone) && !preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
                Yii::$app->session->setFlash('info', '手机号格式错误');
                return '<script>history.back()</script>';
            }
            /*
             * 判断是否点击了部门选项
            /*判断是否只选择了一个部门，若只选择一个就修改department_id 字段，反之则修改 include_department_id字段*/
            if (isset(Yii::$app->request->post('User')['department'])) {
                $departments = Yii::$app->request->post('User')['department'];
                $num = count($departments);             //所选部门的数量
//                var_dump($departments[0]);die;
                /*判断是否只选择了一个部门，若只选择一个就修改department_id 字段，反之则修改 include_department_id字段*/
                if ($num == 1) {
                    $model->department_id = $departments[0];
                } else {
                    $str = '';
                    foreach ($departments as $k => $v) {
                        $str .= $v . ',';
                    }
                    $str = substr($str, 0, -1);
//                    var_dump($str);die;
//                    $model->department_id = $departments[0];
                    $model->include_department_id = $str;
                }
            } else {
                $model->include_department_id = '';

            }
//            $department_id = Yii::$app->request->post('User')['department_id'];
            $group_id = Yii::$app->request->post('User')['group_id'];
            $model->group_id = $group_id;
            if ($old_password !== $model->password) {
                $model->password = md5($model->password);
            }
            $post_data = Yii::$app->request->post('User');
            $subArray = [];
            foreach ($post_data as $k => $v) {
                $subArray[] = $k;
            }
            if (in_array("is_staff", $subArray)) {
                $is_staff = Yii::$app->request->post('User')['is_staff'];
                $true_staff = User::findOne($id);
                if ($is_staff == 0 && $true_staff->is_staff == 1) {
                    $model->is_staff = 0;
                    $model->dimission_time = time();
                } else {
                    $model->is_staff = 1;
                    $model->dimission_time = '';
                }
            }
            if ($model->save()) {
                /*操作跟踪s*/
                $log_title = "修改用户" . $model["username"];
                $log_text = json_encode(Yii::$app->request->post('User'));
                $this->addLog($log_title, $log_text);
                /*操作跟踪e*/
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('info', '修改失败');
                return '<script>history.back()</script>';
            }
        } else {
            $menuids = UserRoute::find()->select('menuids')->where(['userid' => $id])->asarray()->one();
            $model->menuids = explode(',', $menuids['menuids']);
            $regin = Regions::find()->select('p_region_id')->where(['region_id' => $model->domain_id])->asArray()->one();
            $model->province = $regin['p_region_id'];
            $model->dimission_time = \Yii::$app->formatter->asDate($model->dimission_time, 'yyyy-MM-dd');
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = User::findOne($id);
        $this->findModel($id)->delete();
        /*操作跟踪s*/
        $log_title = "删除用户" . $model["username"];
        $log_text = "删除用户" . $model["username"];
        $this->addLog($log_title, $log_text);
        /*操作跟踪e*/
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    //根据传入部门id 获取分组信息
    public function actionGroup()
    {
        $id = $_GET['id'];
        $group = UserGroup::find()
            ->where(['department_id' => $id])
            ->asArray()
            ->all();
        $data = '<option value="0">无分组</option>';
        if (isset($_GET['pid'])) {
            $selectid = User::find()
                ->where(['id' => $_GET['pid']])
                ->asArray()
                ->one();
        }
        if (count($group) > 0) {
            foreach ($group as $v) {
                if (isset($selectid)) {
                    if ($selectid['group_id'] == $v['id']) {
                        $select = 'selected = "selected"';
                    } else {
                        $select = '';
                    }
                } else {
                    $select = '';
                }
                $data .= "<option value='" . $v['id'] . "' $select  >" . $v['name'] . "</option>";
            }
        }
        return $data;
    }


    //根据传入地区id 获取部门
    public function actionDepartment()
    {
        $id = $_GET['id'];
        $branches = UserDepartment::find()
            ->where(['domain_id' => $id])
            ->asArray()
            ->all();
        $data = '';
        if (isset($_GET['pid'])) {
            $selectid = User::find()
                ->where(['id' => $_GET['pid']])
                ->asArray()
                ->one();
        }
        if (count($branches) > 0) {
            foreach ($branches as $branche) {
                if (isset($selectid)) {
                    if ($selectid['department_id'] == $branche['id']) {
                        $select = 'selected = "selected"';
                    } else {
                        $select = '';
                    }
                } else {
                    $select = '';
                }
                $data .= "<option value='" . $branche['id'] . "' $select  >" . $branche['name'] . "</option>";
            }
        } else {
            $data .= "<option value='0' >-</option>";
        }
        return $data;
    }

    /*
     * 获取部门*/
    public function actionGetDepartment()
    {
        $company = UserDepartment::find()
            ->select(['id', 'name'])
            ->orderBy("id asc")
            ->asArray()
            ->all();
        $data = '';
        if (count($company) > 0) {
            $data = "<option>请选择部门</option>";
            foreach ($company as $v) {
                $select = '';
                $data .= "<option value='" . $v['id'] . "' $select  >" . $v['name'] . "</option>";
            }
        } else {
            $data .= "<option>暂无部门</option>";
        }
        return $data;
    }


    //后台分配菜单权限
    public function actionMenuSet()
    {
        $userid = $_GET['id'];
        $userMenu = new User();
        $userdata = $userModel::find()
            ->select(['off_user_sign.id as id', 'off_user_sign.user as user', 'off_user_sign.type as type', 'off_user_sign.time as time', 'off_user.name as name'])
            ->from('off_user_menu a')
            ->where(['userid' => $userid])
            ->leftJoin('off_user', 'off_user_sign.user = a.id')
            ->all();
        $menu = Menu::find()->all();
        $model = $this->findModel($userid);
        $model = ['available' => $menu, 'assigned' => $userdata];
        return $this->render('menuset', [
            'model' => $model,
            'idField' => 1,
            'usernameField' => 2,
            'fullnameField' => 3,
        ]);
    }

    /*用户管理操作跟踪*/
    private function addLog($log_title, $log_text)
    {
        $userLog = new UserLog();
        $uid = \Yii::$app->user->id;
        $user = User::findOne($uid);
        $userLog->user_id = $uid;
        $userLog->type = 3;//登录
        $userLog->log_title = $log_title;
        $userLog->log_text = $log_text;
        $userLog->add_time = time();

        if (!$userLog->save()) {
            $this->setError($userLog->errors);
            return false;
        }

        return true;
    }

    public function actionClear()
    {
        $userid = $_GET['id'];
        $people = User::find()
            ->where(['id' => $userid])
            ->one();
        if (empty($people->phone_imei)) {
            echo '<script>alert("该账号未绑定串号！");history.back()</script>';
            return;
        } else {
            $rescord = PutImei::find()
                ->where(['user_id' => $userid])
                ->andWhere(['status' => 1])
                ->asArray()
                ->all();
            if (!empty($rescord)) {
                echo '<script>alert("该账号已提报串号修改！");history.back()</script>';
                return;
            }
            $people->phone_imei = '';
            $people->imei_time = '';
            $people->phone_brand = '';

            if ($people->save()) {
                /*操作跟踪s*/
                $log_title = "清除串号";
                $log_text = "清除串号";
                if ($this->addLog($log_title, $log_text)) {
                    echo '<script>alert("清除成功！");history.back()</script>';
                }
            }
        }
    }
}
