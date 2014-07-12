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
use FormObject\Field\SelectOneField;
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

/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = Form::create($factory);

$category2 = SelectOneField::create('category2','User Category 2');
$category2->setSrc($categories)->setClassName('RadioButtonsField');

$grouper = new OptionGrouper(function($grouper, $item){
    if(in_array($item, array('Customer','Organisation','Organisation'))){
        return 'Business';
    }
    return 'Personal';

});

$grouper2 = new OptionGrouper(function($grouper, $item){
    if(in_array($item, array('Customer','Organisation','Organisation'))){
        return 'Business';
    }
    return 'Personal';

});

$grouper2->setTitle('Business','Work')->setTitle('Personal','Home');

$form->push(

    TextField::create('name')
               ->setTitle('Please enter your name')
               ->setValue('Billy'),

    TextField::create('surname')
               ->setTitle('Please enter your surname')
               ->setValue('Talent'),

    CheckboxField::create('rememberMe')
                   ->setTitle('Remember Me'),


    BooleanRadioField::create('rememberMyRadio')
                       ->setTitle('Remember my radio')
                       ->setStringForTrue('Remember my radio')
                       ->setStringForFalse('Forget my radio')
                       ->setValue(TRUE),

    TextField::create('message')
               ->setTitle('Message')
               ->setValue('')
               ->setMultiLine(TRUE),

    SelectOneField::create('category','User Category')
                  ->setSrc($categories)
                  ->setGrouper($grouper),

    SelectOneField::create('category2','User Category 2')
                  ->setSrc($categories)
                  ->setGrouper($grouper2)
);

$nameValidator = new TextValidator();
$nameValidator->required = FALSE;
$nameValidator->minLength = 3;
$nameValidator->setMaxLength = 12;

$surnameValidator = new RequiredValidator;
$surnameValidator->required = TRUE;

$requiredValidator = new BooleanValidator();
$requiredValidator->mustBeTrue = TRUE;

$validator = new SimpleValidator($form);
$validator->set('name', $nameValidator);
$validator->set('surname', $surnameValidator);
$validator->set('rememberMyRadio', $requiredValidator);
$validator->set('message', $requiredValidator);

$form->setValidator($validator);

$data = array();
if($form->wasSubmitted()){
    $data = $form->data;
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';