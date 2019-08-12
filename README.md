# yii2-autoGii
yii2 命令行创建 代码

复制文件到
使用：项目 `console\controller` 目录下：
```sh
1.批量处理
//创建model
php yii auto-gii/model

//创建crud
php yii auto-gii/crud

2.单/多张表处理
 php yii  auto-gii/model 'message,message1'
 
 php yii  auto-gii/crud 'message,message1'

```
创建的model说明：只是在common下生成，并且继承自己重写 `\yii\db\ActiveRecord` 的父类模型文件 `BaseActiveRecord`，
创建crud，在backend的model目录写一个模型文件继承 `common\model` 目录下的模型文件。自己使用要根据业务需求更改
没做命令行匹配，改的频率很低,代码都有注释。
