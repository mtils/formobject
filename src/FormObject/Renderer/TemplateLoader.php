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

        $itemClass = $classHierarchy[0];

        foreach($this->paths as $path){

            // If a custom classname was set, first try a direct hit of it
             if ($itemClass != $pseudoClass) {
                if ($filePath = $this->getExistingPathForClass($path, $pseudoClass)) {
                    $this->cache[$pseudoClass] = $filePath;
                    return $filePath;
                } 
             }

            // If not iterate through the class hierarchy from top to bottom
            foreach($classHierarchy as $class){

                if ($filePath = $this->getExistingPathForClass($path, $class)) {
                    $this->cache[$pseudoClass] = $filePath;
                    return $filePath;
                }
            }
        }
        // No Exceptions inside __toString
        trigger_error("No template for FormItem '$pseudoClass' found", E_USER_ERROR);
    }

    protected function getExistingPathForClass($path, $baseClassName) {

        $fileName = self::phpClassNameToTemplateName(
            $baseClassName,
            $this->fileSuffix,
            $this->filePrefix
        );

        $filePath = "$path$fileName";

        if(file_exists($filePath)){
            return $filePath;
        }
    }
}