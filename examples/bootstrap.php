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
$form = new Form;
$form['name'] = new TextField;
$form['name']->setValue("Billy");
$form['name']->setTitle('Please enter your name');
$form['name']->minLength = 3;
$form['name']->maxLength = 12;

$form['surname'] = new TextField;
$form['surname']->setRequired(TRUE);
$form['surname']->setValue("Talent");
$form['surname']->setTitle('Please enter your surname');

$form['rememberMe'] = new CheckboxField;
$form['rememberMe']->setTitle('Remember Me');

$form['rememberMyRadio'] = new BooleanRadioField;
$form['rememberMyRadio']->trueString = 'Remember my Radio';
$form['rememberMyRadio']->falseString = 'Forget my Radio';
$form['rememberMyRadio']->setValue(TRUE);
$form['rememberMyRadio']->mustBeTrue = TRUE;

// $form->actions['submit'] = new Action();
// $form->actions['submit']->setAction('submit');
// $form->actions['submit']->setTitle('Submit');

$form->fillByGlobals();
$data = array();
if($form->wasSubmitted()){
    $data = $form->data;
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';