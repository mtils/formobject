<?php namespace FormObject\Renderer;

use FormObject\FormItem;

class PhpRenderer implements RendererInterface{

    protected $template = '';

    protected $templateLoader = NULL;

    public function renderFormItem(FormItem $item){
        ob_start();
        include $this->getTemplate($item);
        return ob_get_clean();
    }

    public function getTemplate($item){
        $loader = $this->getTemplateLoader();
        return $loader($item);
    }

    public function addPath($path){
        $this->getTemplateLoader()->addPath($path);
        return $this;
    }

    /**
    * @brief Returns the object which deceides which template to choose
    * 
    * @return TemplateLoaderAbstract
    */
    public function getTemplateLoader(){
        if($this->templateLoader === NULL){
            $this->templateLoader = new TemplateLoader;
        }
        return $this->templateLoader;
    }

    public function setTemplateLoader(TemplateLoaderAbstract $loader){
        $this->templateLoader = $loader;
        return $this;
    }

}