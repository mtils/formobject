<?php

namespace FormObject;

class Renderer{

    protected $template = '';

    public function renderFormItem(FormItem $item){
        ob_start();
        include $this->getTemplate();
        return ob_get_clean();
    }

    public function getTemplate(){
        return $this->template;
    }

    public function setTemplate($tplPath){
        $this->template = $tplPath;
    }

}