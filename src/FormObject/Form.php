<?php

namespace FormObject;

use \ArrayAccess;
use \FormObject\Field\Action;
use \FormObject\Field\HiddenField;
use \FormObject\Validator\ValidatorAdapterInterface;
use FormObject\Validator\SimpleValidator;
use ReflectionClass;

class Form extends FormItem implements ArrayAccess{

    const GET = 'get';

    const POST = 'post';

    const REQUEST = 1;

    const MANUAL = 2;

    /**
    * @brief Holds the Form Fields
    * @var FieldList
    */
    protected $_fields = NULL;

    /**
    * @brief Holds the Form Actions (Submit Button, Reset Button ...)
    * @var FieldList
    */
    public $_actions = NULL;


    protected $action = '';

    protected $method = self::POST;

    protected $_needsValidation = NULL;

    protected $_wasSubmitted = NULL;

    protected $dataOrigin;

    protected $validator = NULL;

    protected $adapterFactory = NULL;

    protected $validatorAdapter = NULL;

    protected $autoRedirectOnPost = FALSE;

    protected $appendCsrfToken = FALSE;

    protected $autoFillByRequest = FALSE;


    /**
    * @brief multipart/form-data
    * @var string
    */
    protected $encType = '';

    public function __construct(AdapterFactoryInterface $adapterFactory){

        $this->adapterFactory = $adapterFactory;

    }

    protected function getEventSuffix(){
        return $this->getName();
    }

    public function getFields(){
        if(!$this->_fields){
            $this->_fields = $this->createFields();
            $this->appendAdditionalFields($this->_fields);
            $eventName = 'form.fields-created.'.$this->getEventSuffix();
            $this->getAdapter()
                   ->getEventDispatcher()
                     ->fire($eventName,array($this->_fields));
        }
        return $this->_fields;
    }

    protected function appendAdditionalFields(FieldList &$fields){
        //do nothing
    }

    public function getActions(){
        if(!$this->_actions){
            $this->_actions = $this->createActions();
            $eventName = 'form.actions-created.'. $this->getEventSuffix();
            $this->getAdapter()
                   ->getEventDispatcher()
                     ->fire($eventName,array($this->_actions));
        }
        return $this->_actions;
    }

    public function getAdapter(){
        return $this->adapterFactory;
    }

    public function shouldAppendCsrfToken(){
        return $this->appendCsrfToken;
    }

    public function setShouldAppendCsrfToken($should){
        $this->appendCsrfToken = $should;
        return $this;
    }

    public function isAutoRedirectOnPostEnabled(){
        return $this->autoRedirectOnPost;
    }

    public function enableAutoRedirectOnPost($enabled=TRUE){
        $this->autoRedirectOnPost = $enabled;
        return $this;
    }

    public function disableAutoRedirectOnPost($disabled=TRUE){
        return $this->enableAutoRedirectOnPost(!$disabled);
    }

    protected function updateAttributes(Attributes $attributes){
        parent::updateAttributes($attributes);
        $attributes['method'] = $this->getMethod();
        $attributes['enctype'] = $this->getEncType();
        $attributes['action'] = $this->getAction();
    }

    public function getValidatorAdapter(){
        if(!$this->validatorAdapter){
            $this->validatorAdapter = $this->adapterFactory->createValidatorAdapter($this, $this->getValidator());
        }
        return $this->validatorAdapter;
    }

    public function setValidatorAdapter(ValidatorAdapterInterface $adapter){
        $this->validatorAdapter = $adapter;
        return $this;
    }

    public function getValidator(){
        if(!$this->validator){
            $validator = $this->createValidator();
            $eventName = 'form.validator-created.'. $this->getEventSuffix();
            $this->getAdapter()
                   ->getEventDispatcher()
                     ->fire($eventName,array($validator));
            $this->setValidator($validator);
        }
        return $this->validator;
    }

    protected function createValidator(){
        return new SimpleValidator($this);
    }

    public function setValidator($validator){
        $this->validator = $validator;
        $this->getValidatorAdapter()->setValidator($this->validator);
        return $this;
    }

    public function getDataOrigin(){
        return $this->dataOrigin;
    }

    protected function createFields(){
        $fields = new FieldList;
        $fields->setForm($this);
        $fields->setName('_root');
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
            $class = new ReflectionClass(get_called_class());
            return self::phpClassNameToCssClassName($class->getShortName());
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
        return $this;
    }

    public function offsetExists($offset){
        return $this->getFields()->offsetExists($offset);
    }

    public function offsetGet($offset){
        return $this->getFields()->offsetGet($offset);
    }

    public function offsetSet($offset, $value){
        return $this->getFields()->offsetSet($offset, $value);
    }

    public function offsetUnset($offset){
        $this->getFields()->offsetUnset($offset);
    }

    public function push(Field $field){
        $this->getFields()->push($field);

        $numArgs = func_num_args();

        if($numArgs > 1){
            $args = func_get_args();
            for($i=1;$i<$numArgs;$i++){
                $this->getFields()->push($args[$i]);
            }
        }

        return $this;
    }

    public function get($name){
        return $this->getFields()->get($name);
    }

    public function __invoke($name){
        return $this->getFields()->__invoke($name);
    }

    public function __call($method, $params){
        return call_user_func_array([$this->getFields(), $method], $params);
    }

    public function getAction(){
        if(!$this->action){
            $this->setAction($this->adapterFactory->getDefaultAction($this));
        }
        return $this->action;
    }

    public function setAction($action){
        $this->action = $action;
        return $this;
    }

    public function getMethod(){
        return $this->method;
    }

    public function setMethod($method){
        $this->method = $method;
        return $this;
    }

    /**
     * @brief Fill the form by an array or ArrayInterface. You can prefix
     *        Fields to isolate some fields for another object and prefix
     *        all of them with $prefix .'__'.
     *        If you do that you can pass an array without that prefix in its keys
     *        and separatly pass the prefix as a second parameter.
     *
     *        You have three options:
     *
     * @example FormObject\Form::fillArray($data):
     *        Will fill the form with all data in $data
     * @example FormObject\Form::fillArray($data,'')
     * @example FormObject\Form::fillArray($data, FALSE)
     *        Will fill the form with all data in $data
     *        if the particular FIELDname does not contain "__"
     * @example FormObject\Form::fillArray($data, 'prefix')
     *        Will fill the form by all data in $data but only
     *        concern fields with the prefix "$prefix__"
     *
     * @param \ArrayInterface $data
     * @param mixed $prefix NULL:regard all fields FALSE|'': Regard unprefixed,
     *                      non-empty string: only with prefix $prefix
     * @return void
     **/
    public function fillByArray($data, $prefix=NULL){

        foreach($this->getDataFields() as $field){

            // If its explicit passed that no prefixes
            // should be concerned, skip all fields without
            // a prefix
            if($prefix === FALSE || $prefix === ''){
                if(mb_strpos($field->getName(), '__')){
                    continue;
                }
            }
            elseif($prefix){

                $nameStart = "{$prefix}__";

                // If a prefix is passed, skip all fields without
                // a prefix
                if(mb_strpos($field->getName(), $nameStart) !== 0){
                    continue;
                }

                $dataKey = str_replace($nameStart, '', $field->getName());
            }
            else{
                $dataKey = $field->getName();
            }

            if(isset($data[$dataKey])){
                $field->setValue($data[$dataKey]);
            }

        }
        $this->_needsValidation = FALSE;
        $this->dataOrigin = self::MANUAL;
    }

    protected function doAutoFillByRequest(){
        $this->fillByGlobals();
    }

    public function fillByRequestArray($request=NULL){

        if(is_null($request)){
            $request = $this->getAdapter()->getRequestAsArray($this->getMethod());
        }

        $this->_wasSubmitted = FALSE;
        foreach($this->getActions() as $action){
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
            $this->dataOrigin = self::REQUEST;
        }
    }

    public function fillByGlobals(){
        if($this->getMethod() == self::GET){
            return $this->fillByRequestArray($_GET);
        }
        elseif($this->getMethod() == self::POST){
            return $this->fillByRequestArray($_POST);
        }
    }

    public function getDataFields($prefix=NULL){
        return $this->getFields()->getDataFields($prefix);
    }

    public function getData($prefix=NULL){

        $this->wasSubmitted();

        $data = array();

        foreach($this->getDataFields() as $field){

            $fieldName = $field->getName();

            if($prefix !== NULL){
                if($prefix === FALSE || $prefix === ''){
                    if(mb_strpos($fieldName,'__') === FALSE){
                        $data[$fieldName] = $field->getValue();
                    }
                }
                else{
                    if(mb_strpos($fieldName,"{$prefix}__") !== FALSE){
                        $cleaned = str_replace("{$prefix}__",'',$fieldName);
                        $data[$cleaned] = $field->getValue();
                    }
                }
            }
            else{
                $data[$fieldName] = $field->getValue();
            }
        }
        return $data;
    }

    public function getSelectedAction(){
        if($this->wasSubmitted()){
            foreach($this->getActions() as $action){
                if($action->isSelected()){
                    return $action;
                }
            }
        }
    }

    public function isValid(){
        $valid = TRUE;
        foreach($this->getFields()->getDataFields() as $field){
            if(!$field->isValid($field->getValue())){
                $valid = FALSE;
            }
        }
        return $valid;
    }

    public function needsValidation(){
        if($this->_needsValidation === NULL){
            $this->fillByRequestArray();
        }
        return $this->_needsValidation;
    }

    public function forceValidation(){
        $this->_needsValidation = TRUE;
        return $this;
    }

    public function wasSubmitted(){
        if($this->_wasSubmitted === NULL){
            $this->fillByRequestArray();
        }
        return $this->_wasSubmitted;
    }

    /**
     * @brief Creates a new FormItem
     *
     * @param string $name Name of FormItem
     * @return FormItem
     **/
    public static function create(AdapterFactoryInterface $adapterFactory){
        $class = get_called_class();
        return new $class($adapterFactory);
    }

    public function __toString(){

        try{
            return $this->adapterFactory->getRenderer()->renderFormItem($this);
        }
        // No exceptions inside __toString
        catch(\Exception $e){
            return $e->getMessage() . " Line:" . $e->getLine() . " File:" . $e->getFile();
            trigger_error($e->getMessage(),E_USER_WARNING);
        }
        return "";
    }

    public function copy(){
        $copy = static::create($this->adapterFactory);
        $copy->setName($this->getName())
             ->setEncType($this->getEncType());
        return $copy;
    }
}