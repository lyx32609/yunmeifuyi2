<?php
namespace backend\controllers;

use Yii;
use backend\models\UploadForm;
use yii\web\UploadedFile;
use common\components\UploadImg;
use common\components\UploadFile;
use yii\helpers\Json;




/**
 * PetitionController implements the CRUD actions for Petition model.
 */
class WebUpLoaderController extends BaseController
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'upload'=> ['POST','GET'],
                    'upload-file'=> ['POST','GET'],
                ],
            ],
        ];
    }

    /*上传图片-webUploader*/
    public function actionUpload()
    {
        try
        {
            $model = new UploadImg();
            $info = $model->upImage();

            $info && is_array($info) ?
            exit(Json::htmlEncode($info)) :
            exit(Json::htmlEncode([
                'code' => 1, 
                'msg' => 'error'
            ]));


        }catch (\Exception $e){
            exit(Json::htmlEncode([
                'code' => 1, 
                'msg' => $e->getMessage()
            ]));
        }
    }

    /*上传文件*/
    public function actionUploadFile()
    {
        header("Content-Type:text/html;charset=utf-8");
        try
        {
            $model = new UploadFile();
            $info = $model->upFile();
            
            $info && is_array($info) ?
            exit(Json::htmlEncode($info)) :
            exit(Json::htmlEncode([
                'code' => 1, 
                'msg' => 'error'
            ]));


        }catch (\Exception $e){
            exit(Json::htmlEncode([
                'code' => 1, 
                'msg' => $e->getMessage()
            ]));
        }
    }


    /*上传文件-muluploader*/
    public function mulUploadFile($model)
    {
        $relativePath = 'uploads\official' . '\\' . date('Y-m-d', time()) . '\\';
        $uploadPath =  $relativePath;
        if (!is_dir($uploadPath)){
            mkdir($uploadPath);
        }
        $extension = ['pdf','xls','txt','doc','docx','xlsx'];
        $uploadmodel = new UploadForm();
        $uploadmodel->file = UploadedFile::getInstances($model, 'file');
        if ($uploadmodel->file) {
            foreach ($uploadmodel->file as $file) {
                if (!in_array($file->extension, $extension)){
                    echo '<script>alert("请上传文件");history.back()</script>';
                }
                $ret = $file->saveAs($uploadPath . $file->baseName . '.' . $file->extension);
                if ($ret){
                    $fileName = $file->baseName . "." . $file->extension . ':' . $file->size . ':' . $file->name ;
                    $uploadSuccessPath[] = $relativePath . $fileName;
                }
            }
            $imgpath = implode(',', $uploadSuccessPath);
            return $imgpath;
        }
    }

    /*上传图片-mulUploader*/
    public function mulUploadImg($model)
    {
        $relativePath = 'uploads\official' . '\\' . date('Y-m-d', time()) . '\\';
        $uploadPath = 'http://fulamei.admin.com\\' . $relativePath;
        if (!is_dir($uploadPath)){
            mkdir($uploadPath);
        }
        $extension = ['jpeg','jpg','png','gif'];
        $uploadmodel = new UploadForm();
        $uploadmodel->file = UploadedFile::getInstances($model, 'master_img');
        if ($uploadmodel->file) {
            foreach ($uploadmodel->file as $file) {
                if (!in_array($file->extension, $extension)){
                    echo '<script>alert("请上传图片");history.back()</script>';
                }
                $ret = $file->saveAs($uploadPath . $file->baseName . '.' . $file->extension);
                if ($ret){
                    $fileName = $file->baseName . "." . $file->extension;
                    $uploadSuccessPath[] = $relativePath . $fileName;
                }
            }
            $imgpath = implode(',', $uploadSuccessPath);
            return $imgpath;
        }
    }

     

    
}
