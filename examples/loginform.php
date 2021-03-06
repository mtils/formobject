<?php

ini_set("display_errors","On");

error_reporting(E_ALL);

require_once "lib/AutoLoader.php";

use FormObject\Registry;
use FormObject\Renderer;
use FormObject\Form;
use FormObject\Field;
use FormObject\Field\TextField;
use FormObject\Field\PasswordField;
use FormObject\Field\Action;
use FormObject\Field\CheckboxField;
use FormObject\Field\BooleanRadioField;
use FormObject\Validator\SimpleValidator;
use FormObject\Validator\TextValidator;
use FormObject\Validator\BooleanValidator;
use FormObject\Validator\RequiredValidator;

Registry::getRenderer()->addPath(dirname(__FILE__).'/themes/bootstrap/templates/forms');

/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = Form::create();

$form->push(

    TextField::create('login')
               ->setTitle('Please enter your name')
               ->setValue('admin'),

    PasswordField::create('surname')
                   ->setTitle('Please enter your surname')
                   ->setValue(''),

    CheckboxField::create('rememberMe')
                   ->setTitle('Remember Me')


);

$nameValidator = new TextValidator();
$nameValidator->required = FALSE;
$nameValidator->minLength = 3;
$nameValidator->setMaxLength = 12;

$requiredValidator = new RequiredValidator;
$requiredValidator->required = TRUE;

$trueValidator = new BooleanValidator();
$trueValidator->mustBeTrue = TRUE;

$validator = new SimpleValidator($form);
$validator->set('login', $nameValidator);
$validator->set('surname', $requiredValidator);
// $validator->set('rememberMyRadio', $trueValidator);
// $validator->set('category', $requiredValidator);

$form->setValidator($validator);

$form->fillByGlobals();
$data = array();
if($form->wasSubmitted()){
    $data = $form->data;
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';