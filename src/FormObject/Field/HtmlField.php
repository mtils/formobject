<?php

namespace FormObject\Field;
use FormObject\Field;
use FormObject\Attributes;

class HtmlField extends RichTextField
{

    protected $inlineJsEnabled = false;

    public function enableInlineJs($enabled=true)
    {
        $this->inlineJsEnabled = $enabled;
        return $this;
    }

    public function isInlineJsEnabled()
    {
        return $this->inlineJsEnabled;
    }

    protected function addRuleCssClassesIfNotAdded()
    {
        parent::addRuleCssClassesIfNotAdded();
        $this->getCssClasses()->append('html');
    }
}
