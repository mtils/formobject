<?php namespace FormObject\Renderer;

use \FormObject\FormItem;
use \UnderflowException;

class TemplateLoader extends TemplateLoaderAbstract{

    public function getTemplatePath(FormItem $item){

        $pseudoClass = $item->getClassName();

        if(isset($this->cache[$pseudoClass])){
            return $this->cache[$pseudoClass];
        }

        $classHierarchy = self::getClassHierachy($item);

        foreach($this->paths as $path){
            foreach(self::getClassHierachy($item) as $class){
                $fileName = self::phpClassNameToTemplateName($class,
                                                             $this->fileSuffix,
                                                             $this->filePrefix);
                $filePath = "$path$fileName";
                if(file_exists($filePath)){
                    $this->cache[$pseudoClass] = $filePath;
                    return $filePath;
                }
            }
        }
        // No Exceptions inside __toString
        trigger_error("No template for FormItem '$pseudoClass' found", E_USER_ERROR);
    }
}