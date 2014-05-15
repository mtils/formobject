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
use FormObject\Field\SelectManyField;


Registry::getRenderer()->addPath(dirname(__FILE__).'/themes/bootstrap/templates/forms');

/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = new Form;
$form->setMethod(Form::GET);

$name = new TextField('name', 'Please enter your name');
$name->setValue('Jennifer');
$name->minLength = 3;
$name->maxLength = 12;

$surname = new TextField('surname','Please enter your surname');
$surname->setRequired(TRUE);
$surname->setValue('Batten');

$rememberMe = new CheckboxField('remember','Remember Me');

$rememberMyRadio = new BooleanRadioField('rememberMyRadio');
$rememberMyRadio->trueString = 'Remember my Radio';
$rememberMyRadio->falseString = 'Forget my Radio';
$rememberMyRadio->setValue(TRUE);
$rememberMyRadio->mustBeTrue = TRUE;

$container = new FieldList('group1', 'Tab One');
$container->setSwitchable(TRUE);
$form->push($container);

$container->push($name)->push($surname)->push($rememberMe)->push($rememberMyRadio);

$category = new SelectOneField('category','User Category');

$categories = array(
    1 => 'Customer',
    2 => 'Co-Worker',
    3 => 'Family',
    4 => 'Organisation',
    5 => 'Prospect'
);

$tags = array(
    1 => 'New',
    2 => 'Partner',
    3 => 'Important',
    4 => 'Reused'
);

$category->setSrc($categories)->setValue(2)->setRequired(TRUE);

$tagsField = SelectManyField::create('tags')->setTitle('Tags')->setSrc($tags);

$category2 = SelectOneField::create('category2','User Category 2');
$category2->setSrc($categories)->setClassName('RadioButtonsField');

$tags2 = SelectManyField::create('tags2')->setTitle('Tags 2')->setSrc($tags);
$tags2->setClassName('MultiCheckboxField');

$container2 = new FieldList('group2', 'Tab Two');
$container2->setSwitchable(TRUE);
$container2->push($category)->push($tagsField)->push($category2)->push($tags2);



$form->push($container2);



$form->actions->push(Action::create('delete')->setTitle('Delete'));

$form('surname')->setValue('Button');
$form->fillByGlobals();

$data = array();
if($form->wasSubmitted()){
    $data = $form->data;
//     print_r($_GET);
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';