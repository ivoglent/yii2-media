<?php
/**
 * @copyright Copyright (c) 2013-2015 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace ivoglent\yii2\media;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 *
 * TinyMCE renders a tinyMCE js plugin for WYSIWYG editing.
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */
class MediaEditor extends InputWidget
{

    /**
     * @var string the language to use. Defaults to null (en).
     */
    public $language;
    /**
     * @var array the options for the TinyMCE JS plugin.
     * Please refer to the TinyMCE JS plugin Web page for possible options.
     * @see http://www.tinymce.com/wiki.php/Configuration
     */
    public $clientOptions = [];
    /**
     * @var bool whether to set the on change event for the editor. This is required to be able to validate data.
     * @see https://github.com/2amigos/yii2-tinymce-widget/issues/7
     */
    public $triggerSaveOnBeforeValidateForm = true;
    protected $assetDir ='';
    public function publishAssets($dir){
        $assets = \Yii::$app->assetManager->publish($dir);
        return $this->assetDir =Config::baseUrl().'/'.$assets[1];
    }
    /**
     * @inheritdoc
     */
    public function run()
    {

        $asset=$this->registerClientScript();
        return $this->render('editor',[
            'asset'=>$asset,
            'hasModel' => $this->hasModel(),
            'model' => $this->model,
            'attribute' =>$this->attribute,
            'name' => $this->name,
            'value' => $this->value,
            'options' =>$this->options
        ]);
    }

    /**
     * Registers tinyMCE js plugin
     */
    protected function registerClientScript()
    {
        $js = [];
        $view = $this->getView();
        $asset= $this->publishAssets(dirname(__FILE__).'/assets');
        $id = $this->options['id'];
        $this->clientOptions['selector'] = "#$id";
        // @codeCoverageIgnoreStart
        if ($this->language !== null) {
            $langFile = "langs/{$this->language}.js";
            $this->clientOptions['language_url'] =$asset. "/{$langFile}";
        }
        // @codeCoverageIgnoreEnd

        $options = Json::encode($this->clientOptions);

        $js[] = "tinymce.init($options);";
        if ($this->triggerSaveOnBeforeValidateForm) {
            $js[] = "$('#{$id}').parents('form').on('beforeValidate', function() { tinymce.triggerSave(); });";
        }
        $view->registerJs(implode("\n", $js));
        return $asset;
    }
}
