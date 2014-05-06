<?php

ini_set("display_errors","On");

error_reporting(E_ALL);

require_once "lib/AutoLoader.php";

use \FormObject\Registry;
use \FormObject\Renderer;
use \FormObject\Form;
use \FormObject\Field;
use \FormObject\Field\TextField;

Registry::getRenderer()->addPath(dirname(__FILE__).'/themes/simple');

/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = new Form;
$form->fields['name'] = new TextField;
$form->fields['name']->setValue("Billy");
$form->fields['name']->setTitle('Please enter your name');

$form->fields['surname'] = new TextField;
$form->fields['surname']->setValue("Talent");
$form->fields['surname']->setTitle('Please enter your surname');

echo $form;

echo "\n".$form['name']->getValue();
echo "\n".$form['surname']->getValue();

$data = array('name'=>'Smith','surname'=>'Steven');

$form->fillByArray($data);
$form['name']->addCssClass('important');

echo $form;

echo "\n";