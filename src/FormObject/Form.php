<?php

namespace FormObject;

use ArrayAccess;
use ReflectionClass;

use FormObject\Field\Action;
use FormObject\Field\HiddenField;

use FormObject\Validator\ValidatorInterface;
use FormObject\Validator\FactoryInterface;
use FormObject\Validator\SimpleFactory;
use FormObject\Validator\SimpleValidator;

use FormObject\Http\ActionUrlProviderInterface;
use FormObject\Http\RequestUriActionUrlProvider;
use FormObject\Http\RequestProviderInterface;
use FormObject\Http\GlobalsRequestProvider;

use FormObject\Renderer\RendererInterface;
use FormObject\Renderer\PhpRenderer;

use FormObject\Event\DispatcherInterface;
use FormObject\Event\Dispatcher;

class Form extends FormItem implements ArrayAccess{

    const GET = 'get';

    const POST = 'post';

    const REQUEST = 1;

    const MANUAL = 2;

    protected static $defaultActionProvider;

    protected static $renderer;

    protected static $validatorFactory;

    protected static $requestProvider;

    protected static $eventDispatcher;

    protected static $formModifiers = [];

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

    protected $verb;

    protected $_needsValidation = NULL;

    protected $_wasSubmitted = NULL;

    protected $dataOrigin;

    protected $validator = NULL;

    protected $autoRedirectOnPost = FALSE;

    protected $appendCsrfToken = FALSE;

    protected $autoFillByRequest = FALSE;

    protected $_ignoreFillIfSubmitted = TRUE;

    protected $_throwValidationErrors = TRUE;

    protected $_autoValidated = FALSE;

    /**
    * @brief multipart/form-data
    * @var string
    */
    protected $encType = '';

    protected function getEventSuffix(){
        return $this->getName();
    }

    public function getFields(){

        if(!$this->_fields){

            $this->setFields($this->createFields());

        }

        return $this->_fields;
    }

    public function setFields(FieldList $fields){

        $this->_fields = $fields;

        $this->_fields->setName('_root');

        $this->_fields->setForm($this);

        $this->fireEvent('form.fields-setted',[$this->_fields]);

        return $this;

    }

    protected function createFields(){
        $fields = new FieldList;
        $fields->setForm($this);
        $fields->setName('_root');
        return $fields;
    }

    public function getActions(){

        if(!$this->_actions){

            $this->setActions($this->createActions());

        }
        return $this->_actions;
    }

    public function setActions(FieldList $actions){

        $this->_actions = $actions;

        $this->_actions->setName('_actions');

        $this->_actions->setForm($this);

        $this->fireEvent('form.actions-setted',[$this->_actions]);

        return $this;

    }

    protected function createActions(){
        $actions = new FieldList;
        $actions->setForm($this);

        $actions->push(Action::create('submit')->setTitle('Submit'));

        return $actions;
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

    public function getValidator(){

        if(!$this->validator){

            $validator = static::getValidatorFactory()->createValidator($this);

            $this->setValidator($validator);
        }

        return $this->validator;

    }

    public function setValidator(ValidatorInterface $validator){

        $this->validator = $validator;

        $this->fireEvent('form.validator-setted',[$validator]);

        return $this;
    }

    public function getDataOrigin(){
        return $this->dataOrigin;
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
        $this->performAutoValidation();
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
            $this->getActionUrlProvider()->setActionUrl($this);
        }
        return $this->action;
    }

    public function setAction($action){
        $this->action = $action;
        return $this;
    }

    public function getVerb(){
        if($this->verb){
            return $this->verb;
        }
        return $this->getMethod();
    }

    public function setVerb($verb){
        $this->verb = $verb;
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

        if($this->_ignoreFillIfSubmitted && $this->wasSubmitted()){
            return;
        }

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
            $request = $this->getRequestProvider()->getRequestAsArray($this->getMethod());
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

        $this->performAutoValidation();

        return $this->collectData($prefix);

    }

    protected function performAutoValidation(){

        if(!$this->_autoValidated){

            if($this->wasSubmitted()){

                $this->getValidator()->validate($this->collectData());

            }

            $this->_autoValidated = TRUE;
        }

    }

    protected function collectData($prefix=NULL){

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
    public static function create($name=NULL){
        $form = new static();
        if($name){
            $form->setName();
        }
        return $form;
    }

    public function __toString(){

        static::callFormModifiers($this);

        try{
            return $this->getRenderer()->renderFormItem($this);
        }
        // No exceptions inside __toString
        catch(\Exception $e){
            return get_class($e) .': '. $e->getMessage() . " Line:" . $e->getLine() . " File:" . $e->getFile();
            trigger_error($e->getMessage(),E_USER_WARNING);
        }
        return "";
    }

    public function copy(){
        $copy = static::create();
        $copy->setName($this->getName())
             ->setEncType($this->getEncType());
        return $copy;
    }

    public function ignoreFillIfSubmitted($ignore=TRUE){
        $this->_ignoreFillIfSubmitted = $ignore;
        return $this;
    }

    public function isFillIgnoredIfSubmitted(){
        return $this->_ignoreFillIfSubmitted;
    }

    public function throwValidationException($doThrow=TRUE){
        $this->_throwValidationErrors = $doThrow;
        return $this;
    }

    public function areValidationExceptionsThrown(){
        return $this->_throwValidationErrors;
    }

    public static function getActionUrlProvider(){
        if(!static::$defaultActionProvider){
            static::$defaultActionProvider = new RequestUriActionUrlProvider;
        }
        return static::$defaultActionProvider;
    }

    public static function setActionUrlProvider(ActionUrlProviderInterface $provider){
        static::$defaultActionProvider = $provider;
    }

    public static function getRenderer(){
        if(!static::$renderer){
            static::$renderer = new PhpRenderer();
        }
        return static::$renderer;
    }

    public static function setRenderer(RendererInterface $renderer){
        static::$renderer = $renderer;
    }

    public static function getValidatorFactory(){
        if(!static::$validatorFactory){
            static::$validatorFactory= new SimpleFactory();
        }
        return static::$validatorFactory;
    }

    public static function setValidatorFactory(FactoryInterface $factory){
        static::$validatorFactory = $factory;
    }

    public static function getRequestProvider(){
        if(!static::$requestProvider){
            static::$requestProvider = new GlobalsRequestProvider();
        }
        return static::$requestProvider;
    }

    public static function setRequestProvider(RequestProviderInterface $provider){
        static::$requestProvider = $provider;
    }

    public static function getEventDispatcher(){

        if(!static::$eventDispatcher){
            static::$eventDispatcher = new Dispatcher();
        }
        return static::$eventDispatcher;

    }

    public static function setEventDispatcher(DispatcherInterface $dispatcher){
        static::$eventDispatcher = $dispatcher;
    }

    protected function fireEvent($eventPrefix, array $params){
        $eventName = $eventPrefix . '.' . $this->getEventSuffix();
        static::getEventDispatcher()->fire($eventName, $params);
    }

    public static function addFormModifier($modifier){
        if(!is_callable($modifier)){
            throw new InvalidArgumentException('Modifier has to be callable');
        }
        static::$formModifiers[] = $modifier;
    }

    protected static function callFormModifiers(Form $form){
        foreach(static::$formModifiers as $modifier){
            $modifier($form);
        }
    }
}