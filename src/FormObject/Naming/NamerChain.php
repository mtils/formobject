<?php namespace FormObject\Naming;

use FormObject\Form;
use FormObject\Field;

class NamerChain implements NamerInterface
{

    /**
     * @var array
     **/
    protected $namers = [];

    protected $nameCache = [];

    /**
     * Return the title for field or fieldlist $field in $form
     * If you have no title, return null (no lang codes)
     *
     * @param \FormObject\Form $form
     * @param \FormObject\Field $field
     * @return string|null
     **/
    public function getTitle(Form $form, Field $field)
    {
        return $this->getByProperty($form, $field, 'title');
    }

    /**
     * Return the description for field or fieldlist $field in $form
     * If you have no title, return null (no lang codes)
     *
     * @param \FormObject\Form $form
     * @param \FormObject\Field $field
     * @return string|null
     **/
    public function getDescription(Form $form, Field $field)
    {
        return $this->getByProperty($form, $field, 'description');
    }

    /**
     * Return the tooltip for field or fieldlist $field in $form
     * If you have no title, return null (no lang codes)
     *
     * @param \FormObject\Form $form
     * @param \FormObject\Field $field
     * @return string|null
     **/
    public function getTooltip(Form $form, Field $field)
    {
        return $this->getByProperty($form, $field, 'tooltip');
    }

    public function append(NamerInterface $namer)
    {
        $this->namers = array_merge($this->namers, [$namer]);
        return $this;
    }

    public function prepend(NamerInterface $namer)
    {
        $this->namers = array_merge([$namer], $this->namers);
        return $this;
    }

    public function getByClass($class)
    {
        $namers = [];
        foreach ($this->namers as $namer) {
            if ($namer instanceof $class) {
                $namers[] = $namer;
            }
        }
        return $namers;
    }

    protected function getByProperty(Form $form, Field $field, $property)
    {

        $cacheId = $this->getCacheId($form, $field, $property);

        if (isset($this->nameCache[$cacheId])) {

            if ($this->nameCache[$cacheId] === false) {
                return;
            }

            return $this->nameCache[$cacheId];
        }

        if ($title = $this->getFromNamers($form, $field, $property)) {
            $this->nameCache[$cacheId] = $title;
            return $title;
        }

        $this->nameCache[$cacheId] = false;

    }

    protected function getFromNamers(Form $form, Field $field, $property)
    {

        $method = "get$property";

        foreach ($this->namers as $namer) {
            $title = $namer->{$method}($form, $field);
            if ($title !== null) {
                return $title;
            }
        }

    }

    protected function getCacheId(Form $form, Field $field, $property)
    {
        return $form->getName() . '|' . $field->getName() . "|$property";
    }

}