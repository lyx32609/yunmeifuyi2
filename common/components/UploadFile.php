<?php
namespace common\components;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\base\Exception;
use yii\helpers\FileHelper;

/**
 * 文件上传处理
 */
class UploadFile extends Model
{   
    public $file;
    private $_appendRules;
    public function init () 
    {
        parent::init();
        $extensions = Yii::$app->params['webuploader']['baseConfig']['accept']['extensions_file'];
        $this->_appendRules = [
            [['file'], 'file', 'extensions' => $extensions],
        ];
    }

    // public function rules()
    // {
    //     $baseRules = [];
    //     return array_merge($baseRules, $this->_appendRules);
    // }

    /**
     * 
     */
    public function upFile ()
    {
        $model = new static;
        $model->file = UploadedFile::getInstanceByName('file');
        if (!$model->file){
            return false;
        }
        $relativePath = $successPath = '';
        if ($model->validate()) {
            $relativePath = Yii::$app->params['fileUploadRelativePath'];
            $successPath = Yii::$app->params['fileUploadSuccessPath'];
            $fileName = uniqid(36) . '.' . $model->file->extension;
            //$fileName = iconv("UTF-8", "gb2312", $model->file->baseName) .time(). '.' . $model->file->extension;
            if (!is_dir($relativePath)) {
                FileHelper::createDirectory($relativePath);    
            }
            $model->file->saveAs($relativePath . $fileName);
            return [
                'code' => 0,
                'url' => Yii::$app->params['domain'] . $successPath . $fileName,
                'attachment' => $successPath . $fileName
            ];
        } else {
            $errors = $model->errors;
            return [
                'code' => 1,
                'msg' => current($errors)[0]
            ];
        }
    }


}