<?php

namespace FormObject;

use Collection\StringList;
use \ReflectionClass;
use \ReflectionMethod;
use \ReflectionException;
use \Exception;

class FormItem{

    protected $id;

    protected $name;

    /**
    * @brief The Tooltip (title attribute)
    * @var string
    */
    protected $tooltip = "";

    protected $title;

    protected $description;

    /**
    * @brief CSS Classes
    * @var StringList
    */
    protected $cssClasses;

    /**
    * @brief (HTML) Attributes
    * @var Attributes
    */
    protected $attributes = NULL;

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
        $this->getAttributes()->set('id', $id);
        return $this;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
        $this->getAttributes()->set('name', $name);
        return $this;
    }

    public function hasTitle(){
        return (bool)$this->title;
    }

    public function getTitle(){
        if(!$this->title){
            return $this->name;
        }
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
        return $this;
    }

    public function getTooltip(){
        return $this->tooltip;
    }

    public function setTooltip($tooltip){
        $this->tooltip = $tooltip;
        $this->getAttributes()->set('title', $tooltip);
        return $this;
    }

    public function getDescription(){
        return $this->description;
    }

    public function setDescription($description){
        $this->description = $description;
        return $this;
    }

    protected function initCssClasses(){
        if(!$this->cssClasses){
            $this->cssClasses = new StringList;
            $classNames = static::getRelevantCssClassNames(get_class($this));
            foreach($classNames as $className){
                $this->cssClasses->append(self::phpClassNameToCssClassName($className));
            }
            $this->cssClasses->append($this->getName());
            $this->getAttributes()->setRef('class', $this->cssClasses);
        }
    }

    public function getCssClasses(){
        $this->initCssClasses();
        return $this->cssClasses;
    }

    public function addCssClass($class){
        $this->initCssClasses();
        $this->cssClasses->append($class);
        return $this;
    }

    public function getAttributes(){
        if($this->attributes === NULL){
            $this->attributes = new Attributes();
            $this->initCssClasses();
        }
        return $this->attributes;
    }

    public function setAttribute($key, $value){
        if(in_array(strtolower($key), array('id','name','class','title'))){
            throw new Exception("Please use the methods for id, name and class");
        }
        $this->attributes->set($key,$value);
    }

    public function getClassName(){
        return join('', array_slice(explode('\\', get_class($this)), -1));
    }

    public function __toString(){

        try{
            return Registry::getRenderer()->renderFormItem($this);
        }
        // No exceptions inside __toString
        catch(Exception $e){
            trigger_error($e->getMessage(),E_USER_WARNING);
        }
        return "";
    }

    public static function getRelevantCssClassNames($className){

        $classNames = array();
        $class = new ReflectionClass($className);
        if(in_array($class->getShortName(), array('Field','Form'))){
            return $classNames;
        }

        $classNames[] = $class->getShortName();
        while($class = $class->getParentClass()){
            // Makes only sense until here
            if($class->getShortName() == 'Field' || $class->getShortName() == 'Form'){
                break;
            }
            $classNames[] = $class->getShortName();
        }
        return $classNames;
    }

    public static function phpClassNameToCssClassName($className){
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '-$1', $className));
    }

    public function __get($name){
        $methodName = 'get'.ucfirst($name);
        try{
            $refl = new ReflectionMethod($this, $methodName);
            if($refl->isPublic()){
                return $this->{$methodName}();
            }
        }
        catch(ReflectionException $e){
        }
        return NULL;
    }
}