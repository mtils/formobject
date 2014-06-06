<?php

ini_set("display_errors","On");

error_reporting(E_ALL);

require_once "lib/AutoLoader.php";

use FormObject\Registry;
use FormObject\Renderer;
use FormObject\Renderer\PhpRenderer;
use FormObject\AdapterFactorySimple;
use FormObject\Form;
use FormObject\Field;
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


/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = Form::create($factory);

$form->push(

    TextField::create('name')
               ->setTitle('Name')
               ->setValue('Billy'),

    TextField::create('surname')
               ->setTitle('Please enter your surname')
               ->setValue('Owner')

    
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