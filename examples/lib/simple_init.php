<?php

use FormObject\Form;
use FormObject\Renderer\PhpRenderer;
use FormObject\AdapterFactorySimple;

$renderer = new PhpRenderer();
$renderer->addPath(realpath(__DIR__.'/../themes/bootstrap/templates/forms'));

$factory = new AdapterFactorySimple();
$factory->setRenderer($renderer);

Form::setGlobalAdapter($factory);