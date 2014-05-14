<?php

namespace FormObject;

use \ArrayAccess;
use \FormObject\Field\Action;
use \FormObject\Field\HiddenField;

class Form extends FormItem implements ArrayAccess{

    const GET = 'get';

    const POST = 'post';

    const REQUEST = 1;

    const MANUAL = 2;

    /**
    * @brief Holds the Form Fields
    * @var FieldList
    */
    public $fields = NULL;

    /**
    * @brief Holds the Form Actions (Submit Button, Reset Button ...)
    * @var FieldList
    */
    public $actions = NULL;


    protected $action = '';

    protected $method = self::POST;

    protected $_needsValidation = FALSE;

    protected $_wasSubmitted = FALSE;

    protected $dataOrigin;

    /**
    * @brief multipart/form-data
    * @var string
    */
    protected $encType = '';

    public function __construct($data=NULL){

        $this->fields = $this->createFields();
        $this->actions = $this->createActions();

        $this->getId();
        $this->getName();
        $this->getAction();
        $this->getMethod();
        $this->getEncType();
        $this->getAttributes()->set('method',$this->method);

        if(is_object($data)){
            $this->fillBy($data);
        }
        elseif(is_array($data)){
            $this->fillByArray($data);
        }
    }

    public function getDataOrigin(){
        return $this->dataOrigin;
    }

    protected function createFields(){
        $fields = new FieldList;
        $fields->setForm($this);
        $fields->setName('_root');

//         $fields->push(HiddenField::create('_wasSubmitted')->setValue(1));
        return $fields;
    }

    protected function createActions(){
        $actions = new FieldList;
        $actions->setForm($this);

        $actions->push(Action::create('submit')->setTitle('Submit'));

        return $actions;
    }

    public function getId(){
        if(!$this->id){
            $this->setId($this->getName());
        }
        return parent::getId();
    }

    public function getName(){
        if(!$this->name){
            $this->setName('form');
        }
        return parent::getName();
    }

    public function getEncType(){
        if(!$this->encType){
            $this->setEncType('application/x-www-form-urlencoded');
        }
        return $this->encType;
    }
    
    public function setEncType($encType){
        $this->encType = $encType;
        $this->getAttributes()->set('enctype',$encType);
        return $this;
    }

    public function offsetExists($offset){
        return $this->fields->offsetExists($offset);
    }

    public function offsetGet($offset){
        return $this->fields->offsetGet($offset);
    }

    public function offsetSet($offset, $value){
        return $this->fields->offsetSet($offset, $value);
    }

    public function offsetUnset($offset){
        $this->fields->offsetUnset($offset);
    }

    public function push(Field $field){
        $this->fields->push($field);

        $numArgs = func_num_args();

        if($numArgs > 1){
            $args = func_get_args();
            for($i=1;$i<$numArgs;$i++){
                $this->fields->push($args[$i]);
            }
        }

        return $this;
    }

    public function get($name){
        return $this->fields->get($name);
    }

    public function __invoke($name){
        return $this->fields->__invoke($name);
    }

    public function getAction(){
        if(!$this->action){
            if(isset($_SERVER) && isset($_SERVER['REQUEST_URI'])){
                $this->setAction($_SERVER['REQUEST_URI']);
            }
            else{
                $this->setAction('./');
            }
        }
        return $this->action;
    }

    public function setAction($action){
        $this->action = $action;
        $this->getAttributes()->set('action', $action);
        return $this;
    }

//     public function createAttributes(){
//         $attributes = parent::createAttributes();
//         $attributes['action'] = $this->getAction();
//         $attributes['method'] = $this->method;
//         $attributes['enctype'] = $this->encType;
//         return $attributes;
//     }

    public function getMethod(){
        return $this->method;
    }

    public function setMethod($method){
        $this->method = $method;
        $this->getAttributes()->set('method', $method);
        return $this;
    }

    public function fillBy($data){
        foreach($this->getDataFields() as $field){
            print "\n".$field->getId();
        }
        $this->_needsValidation = FALSE;
        $this->dataOrigin = self::MANUAL;
    }

    public function fillByArray($data){
        foreach($this->getDataFields() as $field){
            if(isset($data[$field->getName()])){
                $field->setValue($data[$field->getName()]);
            }
        }
        $this->_needsValidation = FALSE;
        $this->dataOrigin = self::MANUAL;
    }

    public function fillByRequest($request){

    }

    public function fillByRequestArray($request){

        $this->_wasSubmitted = FALSE;

        foreach($this->actions as $action){
            if(isset($request[$action->getAction()]) && $request[$action->getAction()] == $action->getValue()){
                $action->setSelected(TRUE);
                $this->_wasSubmitted = TRUE;
            }
        }

        if($this->_wasSubmitted){
            foreach($this->getDataFields() as $field){
                $fieldName = $field->getName();
                if(isset($request[$fieldName])){
                    $field->setFromRequest($request[$fieldName]);
                }
                else{
                    $field->setFromRequest(NULL);
                }
            }
            $this->_needsValidation = TRUE;
        }
    }

    public function fillByGlobals(){
        if($this->method == self::GET){
            return $this->fillByRequestArray($_GET);
        }
        elseif($this->method == self::POST){
            return $this->fillByRequestArray($_POST);
        }
    }

    public function getDataFields(){
        return $this->fields->getDataFields();
    }

    public function getData(){
        $data = array();
        foreach($this->getDataFields() as $field){
            $fieldName = $field->getName();
            $data[$fieldName] = $field->getValue();
        }
        return $data;
    }

    public function getSelectedAction(){
        foreach($this->actions as $action){
            if($action->isSelected()){
                return $action;
            }
        }
    }

    public function isValid(){
        $valid = TRUE;
        foreach($this->fields->getDataFields() as $field){
            if(!$field->isValid($field->getValue())){
                $valid = FALSE;
            }
        }
        return $valid;
    }
    
    public function needsValidation(){
        return $this->_needsValidation;
    }
    
    public function wasSubmitted(){
        return $this->_wasSubmitted;
    }

    /**
     * @brief Creates a new FormItem
     *
     * @param string $name Name of FormItem
     * @return FormItem
     **/
    public static function create($data=NULL){
        $class = get_called_class();
        return new $class($data);
    }
}