<?php namespace FormObject\Renderer;

use \FormObject\FormItem;

interface RendererInterface{
    public function renderFormItem(FormItem $item);
}