<?php

ini_set("display_errors","On");

error_reporting(E_ALL);

require_once "lib/AutoLoader.php";

use FormObject\Registry;
use FormObject\Renderer;
use FormObject\Form;
use FormObject\Field;
use FormObject\Field\TextField;
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

    TextField::create('name')
               ->setTitle('Please enter your name')
               ->setValue('Billy')
               ->setMinLength(3)
               ->setMaxLength(12),

    TextField::create('surname')
               ->setTitle('Please enter your surname')
               ->setValue('Talent')               
               ->setRequired(TRUE),

    CheckboxField::create('rememberMe')
                   ->setTitle('Remember Me'),

    
    BooleanRadioField::create('rememberMyRadio')
                       ->setTitle('Remember my radio')
                       ->setStringForTrue('Remember my radio')
                       ->setStringForFalse('Forget my radio')
                       ->setValue(TRUE)
                       ->setMustBeTrue(TRUE),

    TextField::create('message')
               ->setTitle('Message')
               ->setValue('')
               ->setRequired(TRUE)
               ->setMultiLine(TRUE)
               
);

$form->fillByGlobals();
$data = array();
if($form->wasSubmitted()){
    $data = $form->data;
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';