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

    protected $langGroup = 'forms';

    protected $parent;

    protected $childs;

    protected $valid = NULL;

    protected $ruleClassesAdded = FALSE;

    /**
     * You can set a different owner to allow automatic translation keys per owner
     *
     * @var object
     **/
    protected $owner;

    public function __construct($name=NULL, $title=NULL){

        if($name !== NULL){
            $this->setName($name);
        }

        if($title !== NULL){
            $this->setTitle($title);
        }
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

    public function getTitle()
    {
        if ($this->title === null && $this->form) {
            return $this->form->_fieldTitle($this);
        }
        return parent::getTitle();
    }

    public function getTooltip()
    {
        if ($this->tooltip === null && $this->form) {
            return $this->form->_fieldTooltip($this);
        }
        return parent::getTooltip();
    }

    public function getDescription()
    {
        if ($this->description === null && $this->form) {
            return $this->form->_fieldDescription($this);
        }
        return parent::getDescription();
    }

    public function getLangGroup()
    {
        return $this->langGroup;
    }

    public function setLangGroup($langGroup)
    {
        $this->langGroup = $langGroup;
        return $this;
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

        if($this->valid === NULL){
            $this->valid = !$this->form->hasErrors($this->name);
        }

        return $this->valid;
    }

    public function getMessages(){
        return $this->form->getErrors($this->name);
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
            $this->addRuleCssClassesIfNotAdded();
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

    public function isPrefixed()
    {
        $prefixPos = mb_strpos($this->getName(), '__');
        return (is_int($prefixPos) && $prefixPos > 0);
    }

    public function hasPrefix($prefix=null)
    {
        if ($prefix === null) {
            return $this->isPrefixed();
        }

        return ($this->getPrefix() == $prefix);
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

    public function getAttributeTitles()
    {
        return [$this->getName()=>$this->getTitle()];
    }

    /**
     * Return the owner of this field. Mostly this is the form but if you
     * adding fields by plugins sometimes its handy to hold a reference to its
     * owner
     *
     * @return object
     **/
    public function getOwner()
    {
        if (!$this->owner) {
            return $this->getForm();
        }

        return $this->owner;
    }

    /**
     * Set a owner of this field
     *
     * @param object $owner
     * @return self
     **/
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    public function hasDifferentOwner()
    {
        return (bool)$this->owner;
    }

    public function resetOwner()
    {
        $this->owner = null;
        return $this;
    }

    protected function addRuleCssClassesIfNotAdded()
    {
        if(!$this->ruleClassesAdded && $this->form){
            foreach($this->form->getRuleNames($this->name) as $ruleName){
                $this->getCssClasses()->append($ruleName);
            }
            $this->ruleClassesAdded = TRUE;
        }
    }

}