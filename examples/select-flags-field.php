<?php

ini_set("display_errors","On");

error_reporting(E_ALL);

require_once "lib/AutoLoader.php";
require_once "lib/simple_init.php";

use FormObject\Form;
use FormObject\Field;
use FormObject\Field\TextField;
use FormObject\Field\Action;
use FormObject\Field\CheckboxField;
use FormObject\Field\BooleanRadioField;
use FormObject\Field\SelectFlagsField;

use FormObject\Validator\SimpleValidator;
use FormObject\Validator\TextValidator;
use FormObject\Validator\BooleanValidator;
use FormObject\Validator\RequiredValidator;
use FormObject\Validator\ValidationException;



/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = Form::create();

$flagItems = ['swimming','snowboarding','music','theater'];

$flagsField = SelectFlagsField::create('interests','Interests')->setSrc($flagItems)->setValue(12);

$form->push(

    TextField::create('name')
               ->setTitle('Please enter your name')
               ->setValue('Billy'),

    TextField::create('surname')
               ->setTitle('Please enter your surname')
               ->setValue('Talent'),
    $flagsField


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

try{
    $data = $form->getData();
    // Continue here
}
catch(ValidationException $e){
    // Redirect here
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';