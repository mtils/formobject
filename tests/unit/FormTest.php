<?php

namespace FormObject\Test;

use FormObject\Field;
use FormObject\Form;
use FormObject\FieldList;
use FormObject\Field\Action;
use PHPUnit\Framework\TestCase;

/**
 * Class FormTest
 * Contains Unit tests for Form Class with focus on getFields method
 */
class FormTest extends TestCase
{

    /**
     * This method tests if the getFields method returns a field list from an empty Form object
     */
    public function testGetFieldsFromEmptyForm()
    {
        $form = new Form();
        $fields = $form->getFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     * This method is designed to test if the getFields method returns a field list with correct data, given we have added specific data to the Form object.
     */
    public function testGetFieldsFromFormWithData()
    {
        $form = new Form();
        $field = new Field('test_field');
        $form->push($field);
        $fields = $form->getFields();
        $this->assertInstanceOf(FieldList::class, $fields);
        $this->assertEquals('test_field', $fields->first()->getName());
    }

    /**
     * This method tests if the getActions method returns a form actions from an empty Form object
     */
    public function testGetActionsFromEmptyForm()
    {
        $form = new Form();
        $actions = $form->getActions();
        $this->assertInstanceOf(FieldList::class, $actions);
    }

    /**
     * This method tests if the getActions method returns form actions with correct data, given we have added specific data to the Form object.
     */
    public function testGetActionsFromFormWithData()
    {
        $form = new Form();
        $action = new Action('test_action');
        $fields = new FieldList();
        $fields->push($action);
        $form->setActions($fields);
        //$form->getActions()->push($action);
        $actions = $form->getActions();
        $this->assertSame($fields, $actions);
        $this->assertEquals('action_test_action', $actions->first()->getName());
    }

}