<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\gii\CodeFile;
use yii\helpers\Console;
use yii\gii\generators\model\Generator;
use yii\gii\generators\crud\Generator as CrudGenerator;

class AutoGiiController extends Controller
{

    /**
     * 不创建模型的表
     * @var array
     */
    public $blackModelTables = [
        'auth_assignment',
        'auth_item',
        'auth_item_child',
        'auth_rule',
        'banner',
        'menu',
        'member',
        'user',
        'miniProgram',
        'region',
    ];

    /**
     * 不创建的crud的table 黑名单
     * @var array
     */
    public $blackCrudTables = [
        'auth_assignment',
        'auth_item',
        'auth_item_child',
        'auth_rule',
        'banner',
        'menu',
        'member',
        'user',
        'miniProgram',
        'region',
    ];


    /**
     * 生成所有 model
     * actionModel
     * @param string $tableName
     * @throws \yii\base\NotSupportedException
     */
    public function actionModel($tableName='')
    {
        $tables = explode(',',$tableName);

        if(!count($tables)){
            $tables = Yii::$app->db->getSchema()->tableNames;
        }

        foreach ( $tables as $table ) {

            if(in_array($table,$this->blackModelTables)){
                continue;
            }

            try {

                $generator = new Generator();

                //配置模板
                $generator->templates = [
                    'Absolute' => Yii::getAlias('@backend/components/giiTemplate/Absolute/model/default')
                ];

                $generator->baseClass  = '\common\models\BaseActiveRecord'; //父类文件
                $generator->generateLabelsFromComments  = true; //添加字段注释
                $generator->template   = 'Absolute';                        //模板
                $generator->queryNs    = 'common\models';                   //命名空间
                $generator->ns         = 'common\models';                   //命名空间
                $generator->modelClass = $this->formatTableName($table);    //格式化类名
                $generator->tableName  = $table;

                $files = $generator->generate();
                $n     = count($files);
                if ( $n === 0 ) {
                    $this->stdout("\n {$table} 没有文件要生成.", Console::FG_RED);
                    continue;
                }
                /** @var CodeFile $file */
                foreach ( $files as $file ) {
                    $file->getRelativePath();
                    $this->writeContentFile($file->path, $file->content);
                }
            } catch ( \Exception $e ) {
                $this->stdout("\n {$table} 生成失败.". "\n", Console::FG_RED);
                $this->stdout("\n" . $e->getTraceAsString() . "\n", Console::FG_RED);
                $this->stdout("\n" . $e->getMessage() . "\n", Console::FG_RED);
                break;
            }
        }
    }

    /**
     * 格式化表名
     * formatTableName
     * @param $tableName
     * @return mixed
     */
    protected function formatTableName($tableName)
    {
        $tableName = str_replace('_', ' ', $tableName);
        $tableName = str_replace('-', ' ', $tableName);
        $tableName = ucwords($tableName);
        return str_replace(' ', '', $tableName);
    }


    /**
     * 生成所有 crud
     * actionCrud
     * @param string $tableName
     * @throws \yii\base\NotSupportedException
     */
    public function actionCrud($tableName='')
    {
        $tables = explode(',',$tableName);

        if(!count($tables)){
            $tables = Yii::$app->db->getSchema()->tableNames;
        }

        foreach ( $tables as $table ) {

            if(in_array($table,$this->blackCrudTables)){
                continue;
            }

            try {
                $generator = new CrudGenerator();

                $generator->templates = [
                    'Absolute' => Yii::getAlias('@backend/components/giiTemplate/Absolute/crud/default'),
                ];

                $tableName = $this->formatTableName($table);

                $this->writeModelFile($tableName);

                $generator->template            = 'Absolute';                                       //模板
                $generator->modelClass          = "backend\models\\".$tableName;                    //模型类
                $generator->controllerClass     = "backend\controllers\\".$tableName."Controller";  //控制器名称
                $generator->viewPath            = $this->formatViewPath($tableName);                //视图路径

                $generator->baseControllerClass = "backend\controllers\\BackendController";         //父控制器
                $generator->searchModelClass    = "backend\models\search\\".$tableName."Search";    //搜索模型名称

                $files = $generator->generate();

                $n     = count($files);
                if ( $n === 0 ) {
                    $this->stdout("\n {$table} 没有文件要生成.", Console::FG_RED);
                    continue;
                }
                /** @var CodeFile $file */
                foreach ( $files as $file ) {
                    $file->getRelativePath();
                    $this->writeContentFile($file->path, $file->content);
                }

            }catch ( \Exception $e ) {
                $this->stdout("\n {$table} 生成失败.". "\n", Console::FG_RED);
                $this->stdout("\n" . $e->getTraceAsString() . "\n", Console::FG_RED);
                $this->stdout("\n" . $e->getMessage() . "\n", Console::FG_RED);
                break;
            }
        }
    }

    /**
     * 格式视图路径名称
     * formatViewPath
     * @param $tableName
     * @return bool|string
     * @throws \Exception
     */
    protected function formatViewPath($tableName)
    {
        $dir = Yii::getAlias("@backend/views/".strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '-', $tableName)));
        if (!is_dir($dir)) {
            $mask = @umask(0);
            $result = @mkdir($dir, 0777, true);
            @umask($mask);
            if (!$result) {
                throw  new \Exception("Unable to create the directory '$dir'.");
            }
        }
        return $dir;
    }


    /**
     * writeModelFile
     * 复制 父类模型 文件
     * @param $tableName
     * @throws \Exception
     */
    protected function writeModelFile($tableName)
    {
        $backendModelFile = Yii::getAlias("@backend/models/".$tableName.".php");
        if(!is_file($backendModelFile)){
            $content = <<<PHP
<?php

namespace backend\models;

use Yii;


class {$tableName} extends \common\models\\{$tableName}
{

}
PHP;
            $this->writeContentFile($backendModelFile,$content);
        }
    }

    /**
     * writeContentFile
     * @param $file
     * @param $content
     * @throws \Exception
     */
    protected function writeContentFile($file,$content)
    {
        if (@file_put_contents($file, $content) === false) {
            throw  new \Exception("写入文件到 '{$file}' 错误.");
        }
        $mask = @umask(0);
        @chmod($file, 0666);
        @umask($mask);
        $this->stdout("\n 生成文件 {$file} 成功 \n", Console::FG_GREEN);
    }

}


