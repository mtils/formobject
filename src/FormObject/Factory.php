<?php namespace FormObject;


class Factory
{

    protected $namespaces = ['FormObject\Field'];

    protected $groupStack = [];

    /**
     * @var callable
     **/
    protected $fieldCreator;

    public function __construct()
    {
        $this->fieldCreator = function($class) {
            return new $class;
        };
    }

    public function __call($method, array $params=[])
    {
        $name = array_shift($params);
        $attributes = count($params) ? $params[0] : null;
        return $this->create($method, $name, $attributes);
    }

    public function create($shortName, $name, $attributes=null)
    {

        $baseName = $this->getBaseClassName($shortName);

        $class = $this->findClass($baseName);

        $field = call_user_func($this->fieldCreator, $class);

        $field->setName($name);

        $this->applyAttributes($field, $attributes);

        return $field;

    }

    public function form($pseudoClass)
    {
        $suffix = substr($pseudoClass, strlen($pseudoClass)-4, strlen($pseudoClass));
        if ($suffix != 'Form') {
            $pseudoClass .= 'Form';
        }
        return Form::create()->setClassName($pseudoClass);
    }

    public function group($attributes, callable $callable)
    {

        $this->groupStack[] = $this->parseAttributes($attributes);

        call_user_func($callable, $this);

        array_pop($this->groupStack);
    }

    public function appendNamespace($namespace)
    {
        $this->namespaces[] = trim($namespace,'\\');
    }

    public function prependNamespace($namespace)
    {
        $this->namespaces[] = trim($namespace,'\\');
    }

    public function createFieldsWith(callable $creator)
    {
        $this->fieldCreator = $creator;
        return $this;
    }

    protected function applyAttributes(Field $field, $attributes)
    {

        foreach ($this->mergedAttributes($attributes) as $key=>$param) {
            $method = "set$key";
            $field->{$method}($param);
        }
    }

    protected function mergedAttributes($attributes)
    {
        $parsedAttributes = $this->parseAttributes($attributes);

        if (!$groupedAttributes = $this->groupedAttributes()) {
            return $parsedAttributes;
        }

        return array_merge($groupedAttributes, $parsedAttributes);
    }

    protected function groupedAttributes()
    {
        if (!$stackCount = count($this->groupStack)) {
            return [];
        }
        return $this->groupStack[$stackCount-1];
    }

    protected function parseAttributes($attributes)
    {

        if ($attributes === null) {
            return [];
        }

        if (is_array($attributes)) {
            return $attributes;
        }

        $attributes = explode('|', (string)$attributes);

        if (count($attributes) == 1 && !strpos($attributes[0],':')) {
            return ['title'=>$attributes[0]];
        }

        $parsed = [];

        foreach ($attributes as $attribute) {

            $row = explode(':', $attribute);

            if (count($row) == 2) {
                $parsed[$row[0]] = $row[1];
                continue;
            }

            if (count($row) == 1) {
                $parsed[$row[0]] = true;
                continue;
            }

        }

        return $parsed;

    }

    protected function findClass($baseName)
    {
        if ($baseName == 'FieldList') {
            return "FormObject\FieldList";
        }

        foreach ($this->namespaces as $namespace) {
            $className = $namespace . '\\' . $baseName;
            if (class_exists($className)) {
                return $className;
            }
        }
    }

    protected function getBaseClassName($shortName)
    {
        $fieldName = ucfirst($this->cleanFieldSuffix($shortName));

        if ($fieldName == 'FieldList') {
            return $fieldName;
        }

        return $fieldName.'Field';
    }

    protected function cleanFieldSuffix($shortName)
    {
        if (substr($shortName, -5) === 'Field') {
            return substr($shortName, 0, strlen($shortName)-5);
        }
        return $shortName;
    }

}