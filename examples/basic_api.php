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
$form = Form::create();
$form->push(
    TextField::create('name')->setValue('Billy')->setTitle('Please enter your name'),
    TextField::create('surname')->setValue('Talent')->setTitle('Please enter your surname'),
);

echo $form;

echo "\n".$form['name'];
echo "\n".$form['surname'];

$data = array('name'=>'Smith','surname'=>'Steven');

$form->fillByArray($data);
$form('name')->addCssClass('important');

echo $form;

echo "\n";