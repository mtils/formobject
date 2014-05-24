<?php namespace FormObject;

use FormObject\Attributes;

class Field extends FormItem{

    /**
    * @brief The assosiated Form Object
    * @var Form
    * 
    */
    protected $form;

    protected $value;

    protected $parent;

    protected $childs;

    protected $valid = NULL;

    protected $ruleClassesAdded = FALSE;

    /**
    * @brief The field validator
    * @var Validator
    * 
    */
    protected $validator = NULL;

    public function __construct($name=NULL, $title=NULL){

        if($name !== NULL){
            $this->setName($name);
        }

        if($title !== NULL){
            $this->setTitle($title);
        }
    }

    public function initCssClasses(){
        parent::initCssClasses();
        if(!$this->ruleClassesAdded && $this->form){
            foreach($this->form->getValidatorAdapter()->getRuleNames($this->name) as $ruleName){
                $this->cssClasses->append($ruleName);
            }
            $this->ruleClassesAdded = TRUE;
        }
        return $this->cssClasses;
    }


    public function getId(){
        if(!$this->id){
            $this->setId($this->form->getId() . '__' . $this->getName());
        }
        return parent::getId();
    }

    public function getForm(){
        return $this->form;
    }

    public function setForm(Form $form){
        $this->form = $form;
        $this->getId();
        return $this;
    }

    public function getValue(){
        return $this->value;
    }

    public function setValue($value){
        $this->value = $value;
        return $this;
    }

    protected function updateAttributes(Attributes $attributes){
        parent::updateAttributes($attributes);
        $attributes['value'] = $this->value;
    }

    public function setFromRequest($value){
        return $this->setValue($value);
    }

    public function isFirst(){
        if($this->parent){
            return $this->parent->isFirstField($this);
        }
        return FALSE;
    }

    public function isLast(){
        if($this->parent){
            return $this->parent->isLastField($this);
        }
        return FALSE;
    }

    public function getParent(){
        return $this->parent;
    }

    public function setParent($parent){
        $this->parent = $parent;
        return $this;
    }

    public function getChilds(){
        return $this->childs;
    }

    public function holdsData(){
        return TRUE;
    }

    public function isValid(){
        if(!$this->form->needsValidation()){
            return TRUE;
        }

        if($this->valid === NULL){
            $this->valid = !$this->form->getValidatorAdapter()->hasErrors($this->name);
        }

        return $this->valid;
    }

    public function isRequired(){
        return $this->form->getValidatorAdapter()->isRequired($this->name);
    }

    /**
    * @brief Same for overloading via __get
    * 
    * @return bool
    */
    public function getRequired(){
        return $this->isRequired();
    }

    public function getMessages(){
        return $this->form->getValidatorAdapter()->getMessages($this->name);
    }

    /**
     * @brief Creates a new Field
     * 
     * @see __construct
     * @param string $name The name of that field
     * @param string $title The title (label) of that field
     * @return Field
     */
    public static function create($name=NULL, $title=NULL){
        $class = get_called_class();
        return new $class($name, $title);
    }
}