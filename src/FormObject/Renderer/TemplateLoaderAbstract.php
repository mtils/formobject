<?php namespace FormObject\Renderer;

use \FormObject\FormItem;
use \ReflectionClass;

abstract class TemplateLoaderAbstract{

    protected $paths = array();

    protected $cache = array();

    public $fileSuffix = '.phtml';
    public $filePrefix = '';

    public function __invoke($item){
        return $this->getTemplatePath($item);
    }

    abstract public function getTemplatePath(FormItem $item);

    public function addPath($path){
        $path = rtrim($path, '/\\') . '/';
        array_unshift($this->paths, $path);
        return $this;
    }

    protected static function getClassHierachy($item){
        $classNames = array();
        $class = new ReflectionClass($item);
        $classNames[] = $class->getShortName();
        while($class = $class->getParentClass()){
            $classNames[] = $class->getShortName();
        }
        return $classNames;
    }

    public static function phpClassNameToTemplateName($className, $suffix, $prefix=''){
        return $prefix.strtolower(
            preg_replace('/(?<=[a-z])([A-Z])/', '-$1', $className)
            ).$suffix;
    }
}