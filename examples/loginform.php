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


Registry::getRenderer()->addPath(dirname(__FILE__).'/themes/bootstrap/templates/forms');

/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = Form::create();

$form->push(

    TextField::create('login')
               ->setTitle('Please enter your name')
               ->setValue('admin')
               ->setMinLength(3)
               ->setMaxLength(12),

    PasswordField::create('surname')
                   ->setTitle('Please enter your surname')
                   ->setValue('')
                   ->setRequired(TRUE),

    CheckboxField::create('rememberMe')
                   ->setTitle('Remember Me')


);

$form->fillByGlobals();
$data = array();
if($form->wasSubmitted()){
    $data = $form->data;
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';