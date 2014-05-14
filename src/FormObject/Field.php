<?php namespace FormObject;

// use FormObject\Validator;

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

    protected $required;

    protected $valid = NULL;

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
        $this->initAttributes($this->getAttributes());
    }

    protected function initAttributes(Attributes $attributes){
        // Do nothing
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
        $this->getAttributes()->set('value',$value);
        return $this;
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

    public function getValidator(){
        if(!$this->validator){
            $this->validator = Registry::getValidatorFactory()
                                         ->createForField($this);
        }
        return $this->validator;
    }

    public function setValidator($validator){
        $this->validator = $validator;
    }

    protected function createValidator(){
        return new Validator();
    }

    public function isValid(){
        if(!$this->form->needsValidation()){
            return TRUE;
        }

        if($this->valid === NULL){
            $this->valid = $this->getValidator()->isValid($this->getValue());
        }
        return $this->valid;
    }

    public function isRequired(){
        return $this->required;
    }

    /**
    * @brief Same for overloading via __get
    * 
    * @return bool
    */
    public function getRequired(){
        return $this->isRequired();
    }

    public function setRequired($required){
        $this->required = $required;
        return $this;
    }

    public function getMessages(){
        return $this->getValidator()->getMessages();
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