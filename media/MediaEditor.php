<?php
/**
 * Created by Ivoglent Nguyen
 * Contact     :  ivoglent@gmail.com
 * Project     : Yii2 Extensions
 * Filename    : MediaEditor.php
 * Datetime    : 8/20/2015 - 4:03 PM
 * Description :
 *
 */
namespace ivoglent\yii2\media;

use yii\base\InvalidConfigException;
use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;


class MediaEditor extends InputWidget
{

    /**
     * @var string $fileManagerUrl : Url to root of file manager directory
     * required
     */
    public $fileManagerUrl ='';
    /**
     * @var string the language to use.
     */
    public $language='vi';
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
        return $this->assetDir =$assets[1];
    }
    public function init(){
        parent::init();
        if(empty($this->fileManagerUrl)) throw new InvalidConfigException("Missing fileManagerUrl config");
        if(empty($this->clientOptions))
            $this->clientOptions =[];
        $this->clientOptions =array_merge_recursive([
            'plugins' => [
                "advlist autolink lists link charmap print preview anchor",
                "searchreplace visualblocks code fullscreen image",
                "insertdatetime media table contextmenu paste responsivefilemanager"
            ],
            'valid_elements'=> '+*[*]',
            'valid_children' => "+body[style]",
            'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image| responsivefilemanager",
            "external_filemanager_path"=>'',
            "filemanager_title"=>"Photo management" ,
            "filemanager_access_key"=>"i5FFXKzEEH67NuTMMqQChfYGMARRViZ" ,
            "external_plugins" =>  [
                "filemanager" => "plugins/responsivefilemanager/plugin.min.js"
            ]
        ],$this->clientOptions);
        //print_r($this->clientOptions);exit;
        if(empty($this->clientOptions['external_filemanager_path']))
        $this->clientOptions['external_filemanager_path']= $this->fileManagerUrl .'/dialog.php?';
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
            'options' =>$this->options,
            'fileManagerUrl' =>$this->fileManagerUrl
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
