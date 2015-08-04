<?php namespace FormObject\Support\Laravel\Validator;

use FormObject\Validator\ValidatorInterface;
use FormObject\Form;
use DomainException;
use Illuminate\Validation\Validator as LaravelValidator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use \Validator as LaravelFactory;
use Session;

class Validator implements ValidatorInterface{

    protected $srcValidator;

    protected $form;

    protected $validated=FALSE;

    protected $messageBag;

    protected $rules = [];

    protected $throwExceptions = TRUE;

    public function __construct(Form $form){
        $this->form = $form;
    }

    public function validate(array $data){

        $srcValidator = $this->getSrcValidator();

        $srcValidator->setRules($this->getParsedRules($data));
        $srcValidator->setAttributeNames($this->buildAttributeNames($this->form));
        $srcValidator->setData($data);

        // This starts Laravels validating
        $res = $srcValidator->passes();
        $this->validated = TRUE;

        return $res;
    }

    public function getSrcValidator(){

        if(!$this->srcValidator){
            $srcValidator = LaravelFactory::make([], $this->getRules());
            $this->setSrcValidator($srcValidator);
        }
        return $this->srcValidator;
    }

    public function setSrcValidator(LaravelValidator $srcValidator){

        $this->srcValidator = $srcValidator;

        return $this;
    }

    public function hasErrors($fieldName){
        return (bool)count($this->getMessages($fieldName));
    }

    public function getMessages($fieldName=null){

        if ($fieldName === null) {
            return $this->getMessageBag()->all();
        }

        $messageBag = $this->getMessageBag();
        $messageKey = $this->fieldNameToMessageKey($fieldName);

        if ($messageBag->has($messageKey)) {
            return $messageBag->get($messageKey);
        }

        // Fix for older form object where validation rules are forced to have
        // also __ delimiters
        $oldMessageKey = str_replace('.', '__', $messageKey);

        return $messageBag->get($oldMessageKey);

    }

    protected function fieldNameToRuleKey($fieldName)
    {
        return preg_replace('/\.[0-9]\./u', '.*.', $this->fieldNameToMessageKey($fieldName));
    }

    protected function fieldNameToMessageKey($fieldName)
    {
        $multiDotted = str_replace(['[',']','__'],['.','.','.'],$fieldName);
        return trim(str_replace('..','.',$multiDotted), '.');
    }

    protected function getMessageBag(){

        if($errors = Session::get('errors')){
            if($errors instanceof ViewErrorBag && $errors->hasBag('default')){
                return $errors->getBag('default');
            }
        }

        if($this->form->wasSubmitted()){
            if(!$this->validated){
                $this->validate($this->form->data);
            }
            if($messages = $this->getSrcValidator()->messages()){
                return $messages;
            }
        }

        return new MessageBag;

    }

    public function createValidationException(){
        if($messages = $this->getSrcValidator()->messages()){
            return new ValidationException($messages);
        }
    }

    /**
     * @param string $fieldName
     * @return array
     */
    public function getRuleNames($fieldName){

        $originalFieldName = $fieldName;
        $fieldName = $this->fieldNameToRuleKey($fieldName);

        $rules = $this->getRules();

        if (!$ruleData = $this->extractRuleData($fieldName, $rules, $originalFieldName)) {
            return [];
        }

        $ruleNames =[];
        foreach ($this->explodeRule($ruleData) as $rule) {
            $ruleNames[] = $this->ruleToName($rule);
        }

        return $ruleNames;
    }

    /**
     * Explode the rules into an array of rules.
     *
     * @param  string|array  $rules
     * @return array
     */
    protected function explodeRule($rule)
    {
        return (is_string($rule)) ? explode('|', $rule) : $rule;
    }

    protected function ruleToName($rule){
        return preg_replace('/[^a-zA-Z0-9]+/u', '-', $rule);
    }

    public function buildAttributeNames($form){

        $attributeNames = array();

        foreach ($form->getDataFields() as $field){
            foreach ($field->getAttributeTitles() as $name=>$title) {
                $attributeNames[$this->fieldNameToMessageKey($name)] = $title;
            }
        }

        return $attributeNames;
    }

    public function getRules(){
        return $this->rules;
    }

    public function getParsedRules($data)
    {

        $rules = $this->getRules();

        $parsedRules = [];

        foreach ($rules as $fieldName=>$policy) {

            if (!$this->isResizableFieldName($fieldName)) {
                $parsedRules[$fieldName] = $policy;
                continue;
            }

            list($key, $subKey) = explode('.*.', $fieldName);

            if (!isset($data[$key]) || !is_array($data[$key])) {
                continue;
            }

            foreach ($data[$key] as $idx=>$val) {
                $keyName = "$key.$idx.$subKey";
                $parsedRules[$keyName] = $policy;
            }

        }

        return $parsedRules;

    }

    public function setRules(array $rules){
        $this->rules = $rules;
        return $this;
    }

    public function getRule($field){
        if(isset($this->rules[$field])){
            return $this->rules[$field];
        }
    }

    public function setRule($field, $rule){
        $this->rules[$field] = $rule;
        return $this;
    }

    public function removeRule($field){
        unset($this->rules[$field]);
    }

    public function addRules(array $rules){

        foreach($rules as $field=>$rule){
            $this->setRule($field, $rule);
        }
        return $this;

    }

    public function throwsExceptions(){
        return $this->throwExceptions;
    }

    public function forceExceptions($force=TRUE){
        $this->throwExceptions = $force;
    }

    protected function isResizableFieldName($fieldName)
    {
        return (bool)strpos($fieldName, '.*.');
    }

    /**
     * @param $fieldName
     * @param $rules
     * @param $originalFieldName
     * @return string
     */
    public function extractRuleData($fieldName, $rules, $originalFieldName)
    {
        if (isset($rules[$fieldName])) {
            return $rules[$fieldName];
        }
        if (isset($rules[$originalFieldName])) {
            return $rules[$originalFieldName];
        }
        return '';
    }

} 
