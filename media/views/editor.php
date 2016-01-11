<?php
/**
 * Created by long.nguyen.
 * Contact     :  ivoglent@gmail.com
 * Project     : Yii2 Extensions
 * Filename    : editor.php
 * Datetime    : 8/20/2015 - 4:03 PM
 * Description :
 *  
 */
$this->context->view->registerJsFile($asset.'/tinymce.min.js');
?>
<?php
if ($hasModel) {
    echo \yii\helpers\Html::activeTextarea($model, $attribute, $options);
} else {
    echo \yii\helpers\Html::textarea($name, $value, $options);
}

?>