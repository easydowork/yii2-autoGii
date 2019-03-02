# yii2-autoGii
yii2 命令行创建 代码

复制文件到
使用：项目 `console\controller` 目录下：
```sh
//创建model
php yii auto-gii/model

//创建crud
php yii auto-gii/crud
```
创建的model说明：只是在common下生成，并且继承自己重写 `\yii\db\ActiveRecord` 的父类模型文件 `BaseActiveRecord`，
创建crud，在backend的model目录写一个模型文件继承 `common\model` 目录下的模型文件。自己使用要根据业务需求更改
没做命令行匹配，改的频率很低,代码都有注释。
效果：

![深度截图_选择区域_20190215135351.png](/uploads/images/201902/15135603465.png "深度截图_选择区域_20190215135351.png")

![深度截图_选择区域_20190215135508.png](/uploads/images/201902/15135610106.png "深度截图_选择区域_20190215135508.png")


