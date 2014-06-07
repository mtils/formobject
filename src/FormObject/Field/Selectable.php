<?php namespace FormObject\Field;

use \IteratorAggregate;

interface Selectable extends IteratorAggregate{
    public function isItemSelected(SelectableProxy $item);
    public function getSrc();
    public function setSrc($src, $extractor=NULL);
    public function isMultiple();
    public function getColumns();
    public function setColumns($columns);
}
