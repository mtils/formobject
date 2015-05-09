<?php namespace FormObject\Field;

use FormObject\Field;

class LiteralField extends Field{

    protected $content = '';

    public function __construct($name, $content=null)
    {
        parent::__construct($name);

        if (is_string($content) ) {
            $this->setContent($content);
        }
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function __toString()
    {
        return $this->content;
    }

}