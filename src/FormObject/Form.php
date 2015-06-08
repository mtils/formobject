<?php namespace FormObject;

use InvalidArgumentException;
use ArrayAccess;
use ReflectionClass;

use Signal\NamedEvent\BusHolderTrait;

use FormObject\Field\Action;

use FormObject\Validation\BrokerInterface;
use FormObject\Validation\GenericBroker;

use FormObject\Http\ActionUrlProviderInterface;
use FormObject\Http\RequestUriActionUrlProvider;
use FormObject\Http\RequestProviderInterface;
use FormObject\Http\GlobalsRequestProvider;
use FormObject\Naming\NamerChain;

use FormObject\Renderer\RendererInterface;
use FormObject\Renderer\PhpRenderer;
use FormObject\Factory;

class Form extends FormItem implements ArrayAccess
{

    use BusHolderTrait;

    const GET = 'get';

    const POST = 'post';

    const REQUEST = 1;

    const MANUAL = 2;

    protected static $defaultActionProvider;

    protected static $renderer;

    protected static $validationBrokerCreator;

    protected static $requestProviderCreator;

    protected static $actionUrlProviderCreator;

    protected static $namerProviders = [];

    protected static $fieldNamer;

    protected static $rendererCreator;

    protected static $requestProvider;

    protected static $staticEventBus;

    protected static $formModifiers = [];

    protected static $factory;

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

    /**
     * Holds all request params as an array
     *
     * @var array
     **/
    protected $requestArray;

    protected $action = '';

    protected $method = self::POST;

    protected $verb;

    protected $actionsSetted = false;

    protected $_wasSubmitted = NULL;

    protected $dataOrigin;

    protected $validationBroker;

    protected $_ignoreFillIfSubmitted = TRUE;

    protected $model;

    /**
    * @brief multipart/form-data
    * @var string
    */
    protected $encType = '';

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

        $this->selectActions();

        return $this;

    }

    protected function createActions(){
        $actions = new FieldList;
        $actions->setForm($this);

        $actions->push(Action::create('submit'));

        return $actions;
    }

    protected function createActionList($actionName='', $actionTitle='')
    {

        $actions = new FieldList;
        $actions->setForm($this);

        if (!$actionName && !$actionTitle) {
            return $actions;
        }

        $action = Action::create($actionName);

        if ($actionTitle) {
            $action->setTitle($actionTitle);
        }

        $actions->push($action);

        return $actions;

    }

    protected function updateAttributes(Attributes $attributes){
        parent::updateAttributes($attributes);
        $attributes['method'] = $this->getMethod();
        $attributes['enctype'] = $this->getEncType();
        $attributes['action'] = $this->getAction();
    }

    public function getValidationBroker()
    {

        if (!$this->validationBroker) {
            $this->validationBroker = static::createValidationBroker($this);
        }

        return $this->validationBroker;

    }

    public function setValidationBroker(BrokerInterface $broker)
    {
        $this->validationBroker = $broker;
        return $this;
    }

    public function getValidator(){

        return $this->getValidationBroker()->getValidator();

    }

    public function setValidator($validator){

        $this->getValidationBroker->setValidator($validator);
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
            return self::phpClassNameToCssClassName($this->getClassName());
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
        $this->getData();
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

    public function __call($method, $params)
    {

        if (strpos($method, 'with') === 0) {
            $fieldClass = substr($method, 4);
            $field = static::__callStatic($fieldClass, $params);
            $this->push($field);
            return $this;
        }

        if ($this->wasStaticOrSelf()) {
            return static::__callStatic($method, $params);
        }

        return call_user_func_array([$this->getFields(), $method], $params);
    }

    public static function __callStatic($method, array $params=[])
    {
        return static::getFactory()->__call($method, $params);
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
            return $this;
        }

        foreach($this->getDataFields() as $field){

            // If its explicit passed that no prefixes
            // should be concerned, skip all fields without
            // a prefix
            if ($this->isOnlyRootPrefix($prefix) && $field->isPrefixed()) {
                continue;
            }

            // A prefix was passed
            elseif($prefix !== NULL){

                // Skip all fields without that prefix
                if(!$field->hasPrefix($prefix)){
                    continue;
                }

                // Prepend the prefix to fieldname and NOT prepend it to the
                // array key
                $arrayKey = str_replace("{$prefix}__", '', $field->getName());
            }

            // No prefix was passed
            else{
                $arrayKey = $field->getName();
            }

            if(isset($data[$arrayKey])){
                $field->setValue($data[$arrayKey]);
            }

        }
        $this->dataOrigin = self::MANUAL;
        return $this;
    }

    public function getErrors($fieldName=null)
    {
        return $this->getValidationBroker()->getErrors($fieldName);
    }

    public function hasErrors($fieldName=null)
    {
        return $this->getValidationBroker()->hasErrors($fieldName);
    }

    public function getRuleNames($fieldName)
    {
        return $this->getValidationBroker()->getRuleNames($fieldName);
    }

    public function setRuleNames($ruleNames)
    {
        $this->getValidator()->setRules($ruleNames);
        return $this;
    }

    protected function wasStaticOrSelf()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2];

        if ($trace['type'] == '::') {
            return true;
        }
        if (!isset($trace['class'])) {
            return false;
        }
        if ($trace['class'] == 'FormObject\Form') {
            return true;
        }
        if (is_subclass_of($trace['class'], 'FormObject\Form')) {
            return true;
        }

        return false;
    }

    protected function isOnlyRootPrefix($prefix)
    {
        return ($prefix === FALSE || $prefix === '' || $prefix === '.');
    }

    protected function getRequestArray()
    {
        if ($this->requestArray !== null) {
            return $this->requestArray;
        }

        $this->requestArray = $this->getRequestProvider()->getRequestAsArray($this->getMethod());

        return $this->requestArray;

    }

    public function fillByRequestArray($request=NULL){

        if(!$this->wasSubmitted()){
            return;
        }

        $request = $request ?: $this->getRequestArray();

        foreach($this->getDataFields() as $field){
            $fieldName = $field->getName();
            if(isset($request[$fieldName])){
                $field->setFromRequest($request[$fieldName]);
            }
            else{
                $field->setFromRequest(NULL);
            }
        }

        $this->dataOrigin = self::REQUEST;
    }

    public function getDataFields($prefix=NULL){
        return $this->getFields()->getDataFields($prefix);
    }

    public function getData($prefix=NULL){

        $this->fillByRequestArray();

        $this->performAutoValidation();

        return $this->collectData($prefix);

    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    protected function performAutoValidation()
    {
        if ($this->wasSubmitted()) {
            $this->getValidationBroker()->check($this->collectData());
        }
    }

    protected function collectData($prefix=NULL){

        $data = array();

        foreach($this->getDataFields() as $field){

            try{

                $fieldName = $field->getName();

                if ($prefix !== NULL) {
                    if ($this->isOnlyRootPrefix($prefix)) {
                        if(!$field->isPrefixed()){
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

            } catch (ValidationException $e) {
            }
        }

        return $data;
    }

    public function getEventSuffix()
    {
        return $this->getName();
    }

    protected function fireEvent($eventPrefix, array $params){
        $eventName = $eventPrefix . '.' . $this->getEventSuffix();
        $this->fire($eventName, $params);
    }

    public function getSelectedAction(){

        if (!$this->wasSubmitted()) {
            return;
        }

        foreach($this->getActions() as $action) {
            if($action->isSelected()){
                return $action;
            }
        }
    }

    public function isValid(){
        $valid = TRUE;
        foreach($this->getFields()->getDataFields() as $field){
            if(!$field->isValid()){
                $valid = FALSE;
            }
        }
        return $valid;
    }

    public function wasSubmitted(){
        if($this->_wasSubmitted === NULL){
            $this->_wasSubmitted = $this->checkIfSubmitted();
        }
        return $this->_wasSubmitted;
    }

    public function fakeSubmit()
    {
        $this->_wasSubmitted = true;
    }

    protected function checkIfSubmitted()
    {
        foreach ($this->getActions() as $action) {
            if ($action->isSelected() == true) {
                return true;
            }
        }
        return false;
    }

    protected function selectActions()
    {

        $request = $this->getRequestArray();

        foreach($this->getActions() as $action) {

            $name = $action->getAction();
            $value = $action->getValue();

            if(isset($request[$name]) && $request[$name] == $value){
                $action->setSelected(TRUE);
            }
        }

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
            $form->setName($name);
        }
        return $form;
    }

    public function __toString(){

        static::callFormModifiers($this);
        $this->fillByRequestArray();

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

    public function _fieldTitle(Field $field)
    {
        return $this->getFieldNamer()->getTitle($this, $field);
    }

    public function _fieldDescription(Field $field)
    {
        return $this->getFieldNamer()->getDescription($this, $field);
    }

    public function _fieldTooltip(Field $field)
    {
        return $this->getFieldNamer()->getTooltip($this, $field);
    }

    public function getFieldNamer()
    {

        if (!isset(static::$fieldNamer)) {
            static::$fieldNamer = new NamerChain;
            static::callNamerProviders(static::$fieldNamer);
        }

        return static::$fieldNamer;

    }

    public static function getActionUrlProvider()
    {

        if (static::$defaultActionProvider) {
           return static::$defaultActionProvider;
        }

        if (static::$actionUrlProviderCreator) {
            static::$defaultActionProvider = call_user_func(
                static::$actionUrlProviderCreator
            );
            return static::$defaultActionProvider;
        }

        static::$defaultActionProvider = new RequestUriActionUrlProvider;

        return static::$defaultActionProvider;
    }

    public static function provideUrlProvider(callable $callable)
    {
        static::$actionUrlProviderCreator = $callable;
    }

    public static function getRenderer()
    {

        if (static::$renderer) {
            return static::$renderer;
        }

        if (static::$rendererCreator) {
            static::setRenderer(call_user_func(static::$rendererCreator));
            return static::$renderer;
        }

        static::setRenderer(new PhpRenderer());

        return static::$renderer;
    }

    public static function setRenderer(RendererInterface $renderer){

        static::$renderer = $renderer;
        static::fireStatic('form.renderer-changed', $renderer);

    }

    public static function provideRenderer(callable $callable)
    {
        static::$rendererCreator = $callable;
    }

    public static function createValidationBroker(Form $form)
    {
        if (static::$validationBrokerCreator) {
            return call_user_func(static::$validationBrokerCreator, $form);
        }

        $broker = new GenericBroker();
        $broker->setForm($form);
        return $broker;
    }

    public static function provideValidationBroker(callable $callable)
    {
        static::$validationBrokerCreator = $callable;
    }

    public static function provideAdditionalNamer(callable $provider)
    {
        static::$namerProviders[] = $provider;
    }

    public static function getRequestProvider(){

        if(static::$requestProvider){
            return static::$requestProvider;
        }

        if (static::$requestProviderCreator) {
            $requestProvider = call_user_func(static::$requestProviderCreator);
            static::$requestProvider = $requestProvider;
            return static::$requestProvider;

        }

        static::$requestProvider = new GlobalsRequestProvider;

        return static::$requestProvider;

    }

    public static function provideRequestProvider(callable $callable)
    {
        static::$requestProviderCreator = $callable;
    }

    public static function addFormModifier($modifier){
        if(!is_callable($modifier)){
            throw new InvalidArgumentException('Modifier has to be callable');
        }
        static::$formModifiers[] = $modifier;
    }

    public static function getFactory()
    {
        if (!static::$factory) {
            static::$factory = new Factory;
        }
        return static::$factory;
    }

    public static function setFactory(Factory $factory)
    {
        static::$factory = $factory;
    }

    protected static function callFormModifiers(Form $form){
        foreach(static::$formModifiers as $modifier){
            $modifier($form);
        }
    }

    protected static function fireStatic($event, $args)
    {
        if (isset(static::$staticEventBus)) {
            static::$staticEventBus->fire($event, $args);
        }
    }

    protected static function callNamerProviders(NamerChain $namer)
    {
        foreach (static::$namerProviders as $provider) {
            $provider($namer);
        }
    }

}