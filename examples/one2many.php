<?php

ini_set("display_errors","On");

error_reporting(E_ALL);

require_once "lib/AutoLoader.php";

use Collection\Map\Extractor;
use FormObject\Registry;
use FormObject\Renderer;
use FormObject\Renderer\PhpRenderer;
use FormObject\AdapterFactorySimple;
use FormObject\Form;
use FormObject\Field;
use FormObject\Field\SelectManyField;
use FormObject\Field\TextField;
use FormObject\Field\Action;
use FormObject\Field\CheckboxField;
use FormObject\Field\BooleanRadioField;

use FormObject\Validator\SimpleAdapter;
use FormObject\Validator\SimpleValidator;
use FormObject\Validator\TextValidator;
use FormObject\Validator\BooleanValidator;
use FormObject\Validator\RequiredValidator;

$renderer = new Renderer\PhpRenderer();
$renderer->addPath(dirname(__FILE__).'/themes/bootstrap/templates/forms');

$factory = new AdapterFactorySimple();
$factory->setRenderer($renderer);

class Interest{

    protected $id;

    protected $name;

    protected $categoryId;

    protected $start;

    protected static $categories = array(
        1 => 'Sports',
        2 => 'Music',
        3 => 'Arts',
        4 => 'Books',
        5 => 'Math'
    );

    public function __construct($id,$name,$categoryId,$start){
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->start = $start;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getCategory(){
        return self::$categories[$this->categoryId];
    }

    public function getStart(){
        return $this->start->format('Y-m-d H:i:s');
    }
}

$interestList = array(
    new Interest(3, 'Play guitar',2,new DateTime('1998-10-25 10:00:00')),
    new Interest(7, 'Calligraphy',4,new DateTime('2004-05-21 13:00:00')),
    new Interest(14,'Karate',1,new DateTime('1982-04-12 17:45:00')),
    new Interest(78,'Calculating PI',5,new DateTime('1977-02-01 03:14:15')),
);


/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = Form::create($factory);

$interests = SelectManyField::create('interests__items','Interests');

$columns = array(
    'getId()'   => 'InterestID',
    'getName()' => 'Description',
    'getCategory()' => 'Category',
    'getStart()' => 'Interested since'
);

$extractor = new Extractor('getId()','getName()');

$interests->setSrc($interestList, $extractor);
$interests->setClassName('MultiCheckboxField');

$interestsRich = SelectManyField::create('interests__itemsRich','Interests detailed');
$interestsRich->setSrc($interestList, $extractor)->setColumns($columns);

$form->push(

    TextField::create('name')
               ->setTitle('Name')
               ->setValue('Billy'),

    TextField::create('surname')
               ->setTitle('Please enter your surname')
               ->setValue('Owner'),
    $interests,
    $interestsRich

);

$nameValidator = new TextValidator();
$nameValidator->required = FALSE;
$nameValidator->minLength = 3;
$nameValidator->setMaxLength = 12;

$surnameValidator = new RequiredValidator;
$surnameValidator->required = TRUE;

$validator = new SimpleValidator($form);
$validator->set('name', $nameValidator);
$validator->set('surname', $surnameValidator);

$form->setValidator($validator);

$data = array();
if($form->wasSubmitted()){
    $data = $form->data;
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';