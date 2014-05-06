<?php namespace FormObject\Validator;

use FormObject\Registry;
use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionException;

class Validator implements ValidatorInterface{

    protected $messages = array();

    protected static $msgTemplates = array();

    public function isValid($value){
        return TRUE;
    }
    public function addMessage($msgKey){
        $this->messages[] = static::parseMessage($msgKey);
    }

    public function getMessages(){
        return $this->messages;
    }

    protected function parseMessage($msgKey){

        $regMessages = Registry::messages();
        if(!is_array($regMessages)){
            $regMessages = array();
        }

        if(isset($regMessages[$msgKey])){
            $msg = $regMessages[$msgKey];
        }
        else{
            $myMessages = static::collectMsgTemplates();
            $msg = $myMessages[$msgKey];
        }
        $vars = $this->getMessageVars();

        return str_replace(array_keys($vars), array_values($vars), $msg);
    }

    protected static function collectMsgTemplates(){
        $className = get_called_class();
        $msgTpls = array();

        $msgTpls = call_user_func(array($className,'getMsgTemplates'));

        $class = new ReflectionClass($className);

        while($class = $class->getParentClass()){
            $msgTpls = array_merge(call_user_func(array($class->getName(),'getMsgTemplates')), $msgTpls);
        }

        return $msgTpls;
    }

    protected function getMessageVars(){
        $refl = new ReflectionClass($this);
        $messageVars = array();
        foreach($refl->getProperties(ReflectionProperty::IS_PUBLIC) as $prop){
            $messageVars['{'.$prop->getName().'}'] = $this->{$prop->getName()};
        }
        return $messageVars;
    }

    protected static function getMsgTemplates(){
        return static::$msgTemplates;
    }
}