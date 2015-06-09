<?php namespace FormObject;

use Collection\StringDictionary;

class Attributes extends StringDictionary{

    public $rowDelimiter = " ";
    public $keyValueDelimiter = '=';
    public $prefix = '';
    public $suffix = '';

    /**
     * Encodes the attribute values
     *
     * @param string $string
     * @return string
     **/
    public static function valueEncode($string){
        return trim(strip_tags(htmlspecialchars($string, ENT_QUOTES)));
    }

    /**
     * Return the html formated attributes
     *
     * @return string
     **/
    public function __toString(){

        if (!count($this)) {
            return '';
        }

        $rows = array();

        foreach($this as $key=>$value){
            $rows[] = "{$key}{$this->keyValueDelimiter}\"" . self::valueEncode("$value") . '"';
        }

        return $this->prefix . implode($this->rowDelimiter, $rows) . $this->suffix;
    }

}