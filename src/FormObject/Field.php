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
            foreach($this->form->getValidator()->getRuleNames($this->name) as $ruleName){
                $this->cssClasses->append($ruleName);
            }
            $this->ruleClassesAdded = TRUE;
        }
        return $this->cssClasses;
    }


    public function getId(){
        if(!$this->id){
            $search = array('[',']');
            $replace = array('.',':');
            $this->setId($this->form->getId() . '__' . 
                        str_replace($search,$replace,$this->getName())
            );
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
            $this->valid = !$this->form->getValidator()->hasErrors($this->name);
        }

        return $this->valid;
    }

    public function getMessages(){
        return $this->form->getValidator()->getMessages($this->name);
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

    public function __toString(){

        try{
            return $this->form->getRenderer()->renderFormItem($this);
        }
        // No exceptions inside __toString
        catch(\Exception $e){
            return get_class($e) .': '. $e->getMessage() . " Line:" . $e->getLine() . " File:" . $e->getFile();
            trigger_error($e->getMessage(),E_USER_WARNING);
        }
        return "";
    }

    public function copy(){
        $copy = static::create($this->name, $this->title);
        if($this->hasTitle()){
            $copy->setTitle($this->title);
        }
        $copy->setTooltip($this->tooltip);
        $copy->setDescription($this->description);
        return $copy;
    }

    public function getPrefix(){

        list($prefix, $name) = $this->getPrefixAndName();

        return $prefix;

    }

    public function getPlainName(){

        list($prefix, $name) = $this->getPrefixAndName();

        return $name;

    }

    public function getPrefixAndName(){

        $tiled = explode('__', $this->getName());

        if(isset($tiled[1])){
            $prefix = $tiled[0];
            $name = $tiled[1];
        }
        else{
            $prefix = '';
            $name = $tiled[0];
        }
        return [$prefix, $name];

    }

}