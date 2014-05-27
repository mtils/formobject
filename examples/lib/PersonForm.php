<?php

use FormObject\Renderer;
use FormObject\Form;
use FormObject\Field;
use FormObject\FieldList;
use FormObject\Field\TextField;
use FormObject\Field\Action;
use FormObject\Field\CheckboxField;
use FormObject\Field\BooleanRadioField;


class PersonForm extends Form{
    public function createFields(){
        $fields = parent::createFields();
        $name = new TextField('name', 'Please enter your name');
        $name->setValue('Jennifer');
        $name->minLength = 3;
        $name->maxLength = 12;

        $surname = new TextField('surname','Please enter your surname');
        $surname->setValue('Batten');

        $rememberMe = new CheckboxField('remember','Remember Me');

        $rememberMyRadio = new BooleanRadioField('rememberMyRadio');
        $rememberMyRadio->trueString = 'Remember my Radio';
        $rememberMyRadio->falseString = 'Forget my Radio';
        $rememberMyRadio->setValue(TRUE);
        $rememberMyRadio->mustBeTrue = TRUE;


    }
}

/**
 * @brief ...
 * @var FormObject\Form
 */
$form = new Form;





$container = new FieldList('group1', 'Group One');
// $container->setSwitchable(TRUE);
$form->push($container);

$container->push($name)->push($surname)->push($rememberMe)->push($rememberMyRadio);


// $form->actions['submit'] = new Action();
// $form->actions['submit']->setAction('submit');
// $form->actions['submit']->setTitle('Submit');

$form->fillByGlobals();
$data = array();
if($form->wasSubmitted()){
    $data = $form->data;
}

include dirname(__FILE__).'/themes/bootstrap/templates/index.phtml';