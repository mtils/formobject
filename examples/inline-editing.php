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
use FormObject\Field\TextField;
use FormObject\Field\HiddenField;
use FormObject\Field\Action;
use FormObject\Field\CheckboxField;
use FormObject\Field\BooleanRadioField;

use FormObject\Validator\SimpleAdapter;
use FormObject\Validator\SimpleValidator;
use FormObject\Validator\TextValidator;
use FormObject\Validator\BooleanValidator;
use FormObject\Validator\RequiredValidator;
use FormObject\Field\SelectOneField;
use FormObject\Field\SelectManyField;
use FormObject\Field\EditManyField;
use FormObject\Field\OptionGrouper;

$renderer = new Renderer\PhpRenderer();
$renderer->addPath(dirname(__FILE__).'/themes/bootstrap/templates/forms');

$factory = new AdapterFactorySimple();
$factory->setRenderer($renderer);


$categories = array(
    1 => 'Customer',
    2 => 'Co-Worker',
    3 => 'Family',
    4 => 'Organisation',
    5 => 'Prospect'
);

class DT extends DateTime{
    public function __toString(){
        return $this->format('Y-m-d H:i:s');
    }
}

$interestList = array(
    array('id'=>3,'name'=>'Play guitar','categoryId'=>2,'start'=> new DT('1998-10-25 10:00:00')),
    array('id'=>7,'name'=> 'Calligraphy','categoryId'=>4,'start'=> new DT('2004-05-21 13:00:00')),
    array('id'=>14,'name'=>'Karate','categoryId'=>1,'start'=> new DT('1982-04-12 17:45:00')),
    array('id'=>78,'name'=>'Calculating PI','categoryId'=>5,'start'=> new DT('1977-02-01 03:14:15')),
);

$columns = array(
    'id'   => 'InterestID',
    'name' => 'Description',
    'categoryId' => 'Category',
    'start' => 'Interested since'
);


$itemForm = Form::create($factory);
$itemForm->push(

    HiddenField::create('id','ID'),
    TextField::create('name','Description'),
    SelectOneField::create('categoryId','Category')->setSrc($categories),
    TextField::create('start', 'Interested since')

);

/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = Form::create($factory);


$form->push(

    $interests = EditManyField::create('items','Interests')
                                ->setValue($interestList)
                                ->setItemForm($itemForm)
);

$interests->addCssClass('draggable')
          ->addCssClass('removable')
          ->addCssClass('addable');

$nameValidator = new TextValidator();
$nameValidator->required = TRUE;
$nameValidator->minLength = 3;
$nameValidator->setMaxLength = 12;

$surnameValidator = new RequiredValidator;
$surnameValidator->required = TRUE;

$requiredValidator = new BooleanValidator();
$requiredValidator->mustBeTrue = TRUE;

$validator = new SimpleValidator($form);
$validator->set('name', $nameValidator);
// $validator->set('surname', $surnameValidator);
// $validator->set('rememberMyRadio', $requiredValidator);
// $validator->set('message', $requiredValidator);

$itemForm->setValidator($validator);

$data = array();
if($form->wasSubmitted()){
    $data = $form->getData();
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';