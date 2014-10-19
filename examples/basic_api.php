<?php

ini_set("display_errors","On");

error_reporting(E_ALL);

require_once "lib/AutoLoader.php";

use FormObject\Renderer\PhpRenderer;
use FormObject\Form;
use FormObject\Field;
use FormObject\Field\TextField;
use FormObject\AdapterFactorySimple;

$renderer = new PhpRenderer();
$renderer->addPath(__DIR__.'/themes/simple');
$adapter = new AdapterFactorySimple();
$adapter->setRenderer($renderer);

Form::setGlobalAdapter($adapter);


/**
 * @brief ...
 * @var \FormObject\Form
 */
$form = Form::create();
$form->push(
    TextField::create('name')->setValue('Billy')->setTitle('Please enter your name'),
    TextField::create('surname')->setValue('Talent')->setTitle('Please enter your surname')
);

// echo $form;

echo "\n".$form['name'];
echo "\n".$form['surname'];

$data = array('name'=>'Smith','surname'=>'Steven');

$form->fillByArray($data);
$form('name')->addCssClass('important');

echo $form;

echo "\n";