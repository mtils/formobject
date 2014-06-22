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
use FormObject\FieldList;
use FormObject\Field\TextField;
use FormObject\Field\Action;
use FormObject\Field\SelectOneGroup;
use FormObject\Field\CheckboxField;
use FormObject\Field\BooleanRadioField;
use FormObject\Field\SelectOneField;
use FormObject\Field\SelectManyField;
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

$container = new FieldList('group1', 'Tab One');
$container->setSwitchable(TRUE);
$form->push($container);

$container->push($name)->push($surname)->push($rememberMe)->push($rememberMyRadio);

$container2 = new FieldList('group2', 'Tab Two');
$container2->setSwitchable(TRUE);
// $container2->push($category)->push($tagsField)->push($category2)->push($tags2);

$linkTypes = array('internal'=>'Internal','external'=>'External');

$selectGroup = SelectOneGroup::create('linkType', 'Link')->setSrc($linkTypes);
$selectGroup->setValue('internal');

$targets = array(
    'firstchild' => 'First Child Page',
    '1' => 'Home',
    '2' => 'Contact',
    '3' => 'About us'
);

$pages = SelectOneField::create('internalTarget','Target')->setSrc($targets);

$selectGroup->push($pages);


$externalTarget = TextField::create('externalTarget','URL');

$selectGroup->push($externalTarget);

$container2->push($selectGroup);

$form->push($container2);



$form->actions->push(Action::create('delete')->setTitle('Delete'));

$form('surname')->setValue('Button');

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
$validator->set('linkType', $requiredValidator);
$validator->set('rememberMyRadio', $trueValidator);
$validator->set('internalTarget', $requiredValidator);
$validator->set('externalTarget', $nameValidator);

$form->setValidator($validator);

$form->fillByGlobals();

$data = array();
if($form->wasSubmitted()){
    $data = $form->data;
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';