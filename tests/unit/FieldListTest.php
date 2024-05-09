<?php

namespace FormObject\Test;

use PHPUnit\Framework\TestCase;
use FormObject\FieldList;
use FormObject\Field;

class FieldListTest extends TestCase{

    /**
     * Test the 'isValid' function of FieldList class
     */
    public function testIsValid(){

        // Create new FieldList
        $fieldList = new FieldList();

        // Create new valid Field
        $field1 = $this->createMock(Field::class);
        $field1->method('holdsData')->willReturn(true);
        $field1->method('isValid')->willReturn(true);

        // Add valid Field to FieldList
        $fieldList->push($field1);

        // Assert that fieldList is valid
        $this->assertTrue($fieldList->isValid());

        // Create new invalid Field
        $field2 = $this->createMock(Field::class);
        $field2->method('holdsData')->willReturn(true);
        $field2->method('isValid')->willReturn(false);

        // Add invalid Field to FieldList
        $fieldList->push($field2);

        // Assert that fieldList is not valid
        $this->assertFalse($fieldList->isValid());
    }
}