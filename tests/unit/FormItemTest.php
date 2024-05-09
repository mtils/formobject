<?php

namespace FormObject\Test;

use FormObject\FormItem;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

class FormItemTest extends TestCase
{

    /**
     * Test the 'getId' method in 'FormItem' class
     */
    public function testGetId()
    {
        // Test setup
        $formItem = new FormItem();
        $expectedValue = "example_id";
        $formItem->setId($expectedValue);

        // Assert method under test returns the expected result
        $this->assertEquals($expectedValue, $formItem->getId());
    }

    /**
     * Another test for 'getId' when no id is explicitly set.
     */
    public function testGetIdWithoutSettingId()
    {
        // Test setup
        $formItem = new FormItem();

        // Assert default state/behavior of getID() method
        $this->assertNull($formItem->getId());
    }

    /**
     * Yet another test for 'getId' ensuring setId returns the formItem for fluent calls.
     */
    public function testGetIdEnsureFluency()
    {
        // Test setup
        $formItem = new FormItem();
        $return = $formItem->setId("fluent_id");

        // Assert that setting an id returns the formItem
        $this->assertEquals($formItem, $return);
    }

    /**
     * Test the 'getName' method in 'FormItem' class.
     */
    public function testGetName()
    {
        // Test setup
        $formItem = new FormItem();
        $expectedValue = "example_name";
        $formItem->setName($expectedValue);

        // Assert method under test returns the expected result
        $this->assertSame($expectedValue, $formItem->getName());
    }

    /**
     * Another test for 'getName' when no name is explicitly set.
     */
    public function testGetNameWithoutSettingName()
    {
        // Test setup
        $formItem = new FormItem();

        // Assert default state/behavior of getName() method
        $this->assertNull($formItem->getName());
    }

    /**
     * Yet another test for 'getName' ensuring setName returns the formItem for fluent calls.
     */
    public function testGetNameEnsureFluency()
    {
        // Test setup
        $formItem = new FormItem();
        $return = $formItem->setName("fluent_name");


        $this->assertSame($formItem, $return);
    }
    /**
     * Test the 'getTitle' method in 'FormItem' class when title is explicitly set.
     */
    public function testGetTitleWithExplicitTitle()
    {
        // Test setup
        $formItem = new FormItem();
        $expectedValue = "example_title";
        $formItem->setTitle($expectedValue);

        // Assert method under test returns the expected result
        $this->assertEquals($expectedValue, $formItem->getTitle());
    }

    /**
     * Test 'getTitle' when no title is explicitly set. In this case, it should return name.
     */
    public function testGetTitleWithoutSettingTitle()
    {
        // Test setup
        $formItem = new FormItem();
        $formItem->setName("example_name");

        // Assert default state/behavior of getTitle() method
        $this->assertEquals("example_name", $formItem->getTitle());
    }

    /**
     * Yet another test for 'getTitle' ensuring setTitle returns the formItem for fluent calls.
     */
    public function testGetTitleEnsureFluency()
    {
        // Test setup
        $formItem = new FormItem();
        $return = $formItem->setTitle("fluent_title");


        $this->assertSame($formItem, $return);
    }
    /**
     * Test the 'getTooltip' method in 'FormItem' class when tooltip is explicitly set.
     */
    public function testGetTooltipWithExplicitTooltip()
    {
        // Test setup
        $formItem = new FormItem();
        $expectedValue = "example_tooltip";
        $formItem->setTooltip($expectedValue);

        // Assert method under test returns the expected result
        $this->assertEquals($expectedValue, $formItem->getTooltip());
    }

    /**
     * Test 'getTooltip' when no tooltip is explicitly set.
     */
    public function testGetTooltipWithoutSettingTooltip()
    {
        // Test setup
        $formItem = new FormItem();

        // Assert default state/behavior of getTooltip() method
        $this->assertNull($formItem->getTooltip());
    }

    /**
     * Yet another test for 'getTooltip' ensuring setTooltip returns the formItem for fluent calls.
     */
    public function testGetTooltipEnsureFluency()
    {
        // Test setup
        $formItem = new FormItem();
        $return = $formItem->setTooltip("fluent_tooltip");


        $this->assertSame($formItem, $return);
    }
    /**
     * Test the 'getDescription' method in 'FormItem' class when description is explicitly set.
     */
    public function testGetDescriptionWithExplicitDescription()
    {
        // Test setup
        $formItem = new FormItem();
        $expectedValue = "example_description";
        $formItem->setDescription($expectedValue);

        // Assert method under test returns the expected result
        $this->assertEquals($expectedValue, $formItem->getDescription());
    }

    /**
     * Test 'getDescription' when no description is explicitly set.
     */
    public function testGetDescriptionWithoutSettingDescription()
    {
        // Test setup
        $formItem = new FormItem();

        // Assert default state/behavior of getDescription() method
        $this->assertNull($formItem->getDescription());
    }

    /**
     * Yet another test for 'getDescription' ensuring setDescription returns the formItem for fluent calls.
     */
    public function testGetDescriptionEnsureFluency()
    {
        // Test setup
        $formItem = new FormItem();
        $return = $formItem->setDescription("fluent_description");


        $this->assertSame($formItem, $return);
    }


    /**
     * Test the 'getCssClasses' method in 'FormItem' class when css classes are not explicitly set.
     */
    public function testGetCssClassesWithoutSettingCssClasses()
    {
        // Test setup
        $formItem = new FormItem();

        // Assert default state/behavior of getCssClasses() method
        $this->assertSame([], iterator_to_array($formItem->getCssClasses()));

        $formItem = new FormItem();
        $formItem->setName('foo');
        $this->assertSame(['foo'], iterator_to_array($formItem->getCssClasses()));
    }

    /**
     * Test the 'getCssClasses' method in 'FormItem' class when css classes are set using addCssClass method.
     */
    public function testGetCssClassesWithSetCssClasses()
    {
        // Test setup
        $formItem = new FormItem();
        $expectedValue = "example_css_class";
        $formItem->addCssClass($expectedValue);

        // Assert method under test returns the expected result
        $this->assertEquals([$expectedValue], $formItem->getCssClasses()->src());
    }

    /**
     * Yet another test for 'getCssClasses' ensuring addCssClass returns the formItem for fluent calls.
    /**
     * Yet another test for 'getCssClasses' ensuring addCssClass returns the formItem for fluent calls.
     */
    public function testGetCssClassesEnsureFluency()
    {
        // Test setup
        $formItem = new FormItem();
        $return = $formItem->addCssClass("fluent_css_class");

        // Assert that setting an css class returns the formItem
        $this->assertEquals($formItem, $return);
    }

    /**
     * Test 'addCssClass' to check if it correctly adds a CSS class to the 'FormItem'.
     */
    public function testAddCssClass()
    {
        // Test setup
        $formItem = new FormItem();
        $cssClass = "new_css_class";
        $formItem->addCssClass($cssClass);

        // Assert method under test adds the CSS class correctly
        $this->assertTrue($formItem->hasCssClass($cssClass));
    }

    /**
     * Test 'addCssClass' to ensure it returns the formItem for fluent calls.
     */
    public function testAddCssClassEnsureFluency()
    {
        // Test setup
        $formItem = new FormItem();
        $return = $formItem->addCssClass("fluent_css_class");

        // Assert that adding a css class returns the formItem
        $this->assertSame($formItem, $return);
    }
    /**
     * Test the 'getAttributes' method in 'FormItem' class when no attributes have been explicitly set.
     */
    public function testGetAttributesWithoutSettingAttributes()
    {
        // Test setup
        $formItem = new FormItem();

        // Assert default state/behavior of getAttributes() method
        $this->assertInstanceOf(\FormObject\Attributes::class, $formItem->getAttributes());
    }

    /**
     * Test the 'getAttributes' method in 'FormItem' class when attributes have been set using setAttribute method.
     */
    public function testGetAttributesWithSetAttributes()
    {
        // Test setup
        $formItem = new FormItem();
        $formItem->setAttribute("example_attribute", "example_value");

        // Assert method under test returns the expected result
        $this->assertEquals("example_value", $formItem->getAttributes()->get("example_attribute"));
    }

    /**
     * Test for 'getAttributes' ensuring setAttribute returns the formItem for fluent calls.
     */
    public function testGetAttributesEnsureFluency()
    {
        // Test setup
        $formItem = new FormItem();
        $return = $formItem->setAttribute("fluent_attribute", "fluent_value");

        // Assert that setting an attribute returns the formItem
        $this->assertEquals($formItem, $return);
    }
}
