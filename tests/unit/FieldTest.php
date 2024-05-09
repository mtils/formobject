<?php

namespace FormObject\Test\Users\Micha\Code\WebUtlils\FormObject\Tests\Unit;

use PHPUnit\Framework\TestCase;
use FormObject\Field;
use FormObject\Form;

class FieldTest extends TestCase
{
    /** @var Field|null */
    private $field = null;

    public function setUp(): void
    {
        $this->field = new Field('testField', 'Test Field');
    }

    public function testGetIdWithoutForm(): void
    {
        $this->assertSame('testField', $this->field->getId());
    }

    public function testGetIdWithForm(): void
    {
        $form = new Form();
        $form->setName('testForm');
        $this->field->setForm($form);
        $this->assertSame('testForm__testField', $this->field->getId());
    }
}