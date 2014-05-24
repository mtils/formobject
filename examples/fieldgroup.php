<?php

ini_set("display_errors","On");

error_reporting(E_ALL);

require_once "lib/AutoLoader.php";

use FormObject\Registry;
use FormObject\Renderer;
use FormObject\Form;
use FormObject\Field;
use FormObject\FieldList;
use FormObject\Field\TextField;
use FormObject\Field\Action;
use FormObject\Field\CheckboxField;
use FormObject\Field\BooleanRadioField;
use FormObject\Field\SelectOneField;
use FormObject\Validator\SimpleValidator;
use FormObject\Validator\TextValidator;
use FormObject\Validator\BooleanValidator;
use FormObject\Validator\RequiredValidator;



Registry::getRenderer()->addPath(dirname(__FILE__).'/themes/bootstrap/templates/forms');

/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = new Form;
$form->setMethod(Form::GET);

$name = new TextField('name', 'Please enter your name');
$name->setValue('Jennifer');

$surname = new TextField('surname','Please enter your surname');
$surname->setValue('Batten');

$rememberMe = new CheckboxField('remember','Remember Me');

$rememberMyRadio = new BooleanRadioField('rememberMyRadio');
$rememberMyRadio->trueString = 'Remember my Radio';
$rememberMyRadio->falseString = 'Forget my Radio';
$rememberMyRadio->setValue(TRUE);

$container = new FieldList('group1', 'Group One');

$form->push($container);

$container->push($name)->push($surname)->push($rememberMe)->push($rememberMyRadio);

$category = new SelectOneField('category','User Category');

$category->setSrc(array(
    1 => 'Customer',
    2 => 'Co-Worker',
    3 => 'Family',
    4 => 'Organisation',
    5 => 'Prospect'
))->setValue(2);

$container2 = new FieldList('group2', 'Group Two');
$container2->push($category);

$form->push($container2);

$form('surname')->setValue('Hunter');

$nameValidator = new TextValidator();
$nameValidator->required = FALSE;
$nameValidator->minLength = 3;
$nameValidator->setMaxLength = 12;

$requiredValidator = new RequiredValidator;
$requiredValidator->required = TRUE;

$trueValidator = new BooleanValidator();
$trueValidator->mustBeTrue = TRUE;

$validator = new SimpleValidator($form);
$validator->set('name', $nameValidator);
$validator->set('surname', $requiredValidator);
$validator->set('rememberMyRadio', $trueValidator);
$validator->set('category', $requiredValidator);

$form->setValidator($validator);

$form->fillByGlobals();
$data = array();
if($form->wasSubmitted()){
    $data = $form->data;
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';