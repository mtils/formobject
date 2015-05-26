<?php namespace FormObject\Naming;

use FormObject\Form;
use FormObject\Field;

interface NamerInterface
{

    /**
     * Return the title for field or fieldlist $field in $form
     * If you have no title, return null (no lang codes)
     *
     * @param \FormObject\Form $form
     * @param \FormObject\Field $field
     * @return string|null
     **/
    public function getTitle(Form $form, Field $field);

    /**
     * Return the description for field or fieldlist $field in $form
     * If you have no title, return null (no lang codes)
     *
     * @param \FormObject\Form $form
     * @param \FormObject\Field $field
     * @return string|null
     **/
    public function getDescription(Form $form, Field $field);

    /**
     * Return the tooltip for field or fieldlist $field in $form
     * If you have no title, return null (no lang codes)
     *
     * @param \FormObject\Form $form
     * @param \FormObject\Field $field
     * @return string|null
     **/
    public function getTooltip(Form $form, Field $field);

}