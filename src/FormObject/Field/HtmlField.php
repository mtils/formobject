<?php

namespace FormObject\Field;
use FormObject\Field;
use FormObject\Attributes;

class HtmlField extends RichTextField
{
    protected function addRuleCssClassesIfNotAdded()
    {
        parent::addRuleCssClassesIfNotAdded();
        $this->getCssClasses()->append('html');
    }
}
