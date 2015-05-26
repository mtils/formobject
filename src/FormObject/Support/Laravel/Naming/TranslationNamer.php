<?php namespace FormObject\Support\Laravel\Naming;

use FormObject\Form;
use FormObject\Field;
use FormObject\Field\Action;
use FormObject\Naming\NamerInterface;
use Illuminate\Translation\Translator;

class TranslationNamer implements NamerInterface
{

    protected $lang;

    protected $namespaces = [''];

    protected $formKeyCache = [];

    protected $cache = [];

    public function __construct(Translator $lang)
    {
        $this->lang = $lang;
    }

    /**
     * {@inheritdoc}
     *
     * @param \FormObject\Form $form
     * @param \FormObject\Field $field
     * @return string|null
     **/
    public function getTitle(Form $form, Field $field)
    {
        return $this->translateProperty($form, $field, 'title');
    }

    /**
     * {@inheritdoc}
     *
     * @param \FormObject\Form $form
     * @param \FormObject\Field $field
     * @return string|null
     **/
    public function getDescription(Form $form, Field $field)
    {
        return $this->translateProperty($form, $field, 'description');
    }

    /**
     * {@inheritdoc}
     *
     * @param \FormObject\Form $form
     * @param \FormObject\Field $field
     * @return string|null
     **/
    public function getTooltip(Form $form, Field $field)
    {
        return $this->translateProperty($form, $field, 'tooltip');
    }

    /**
     * Append a "root" translation key. The translator will search in the following
     * order: $this->root[0] . $formName . $field. Dots in form name will be
     * replaced by / to not destroy your lang arrays
     *
     * @param string $namespace
     * @return self
     **/
    public function appendNamespace($namespace)
    {
        $this->namespaces = array_merge(
            $this->namespaces,
            $this->normalizeNamespace($namespace)
        );
        return $this;
    }

    /**
     * Prepend a "root" translation key. This will be searched first
     *
     * @param string $namespace
     * @return self
     **/
    public function prependNamespace($namespace)
    {
        $this->namespaces = array_merge(
            (array)$this->normalizeNamespace($namespace),
            $this->namespaces
        );
        return $this;
    }

    protected function normalizeNamespace($namespace)
    {
        return rtrim($namespace,':').'::';
    }

    protected function relativeKey(Form $form, Field $field, $property, $langGroup='forms')
    {
        return "$langGroup." . $this->getFieldOwnerKey($field) . '.' . $this->getFieldName($field) . ".$property";
    }

    protected function getFieldOwnerKey(Field $field)
    {

        $ownerName = $field->getOwner()->getName();

        if (isset($this->formKeyCache[$ownerName])) {
            return $this->formKeyCache[$ownerName];
        }

        $name = $ownerName;

        if (ends_with($ownerName,'-form')) {
            $name = substr($ownerName, 0, strlen($ownerName)-5);
        }

        $this->formKeyCache[$ownerName] = $name;

        return $this->formKeyCache[$ownerName];

    }

    protected function getFieldName(Field $field)
    {

        $fieldName = $field->getName();

        if (!$field->hasDifferentOwner()) {
            return $fieldName;
        }

        $parts = explode('__', $fieldName);

        if (count($parts) == 1) {
            return $fieldName;
        }

        array_shift($parts);

        return implode('__', $parts);

    }

    protected function translateProperty(Form $form, Field $field, $property)
    {

        $relativeKey = $this->relativeKey($form, $field, $property, $field->getLangGroup());

        if (isset($this->cache[$relativeKey])) {
            return $this->cache[$relativeKey];
        }

        if ($this->isConfirmation($field)) {
            $title = $this->confirmedTitle($form, $field, $property);
        } else {
            $title = $this->getFirstMatch($relativeKey);
        }

        if (!$title && $field instanceof Action) {
            $fallbackKey = $this->getFallbackActionKey($field, $property);
            $title = $this->getFirstMatch($fallbackKey);
        }

        $this->cache[$relativeKey] = $title;

        return $this->cache[$relativeKey];

    }

    protected function getFirstMatch($relativeKey)
    {
        if ($absoluteKey = $this->getFirstMatchedKey($relativeKey)) {
            return $this->lang->get($absoluteKey);
        }
    }

    protected function getFirstMatchedKey($relativeKey)
    {
        foreach ($this->namespaces as $root) {
            $absoluteKey = "$root$relativeKey";
            if ($this->lang->has($absoluteKey)) {
                return $absoluteKey;
            }
        }
    }

    protected function isConfirmation(Field $field)
    {
        return ends_with($field->getName(), '_confirmation');
    }

    protected function getFallbackActionKey(Action $action, $property)
    {
        return 'forms.actions.'. $action->getShortName() . ".$property";
    }


    protected function confirmedTitle(Form $form, Field $field, $property)
    {

        $fieldName = $field->getName();

        // If in some lang file the confirmation key is excplicit setted
        if ($directHit = $this->getFirstMatch($this->relativeKey($form, $field, $property))) {
            return $directHit;
        }

        // Otherwise try to translate the confirmation text with the source field
        // name

        // If no confirm key setted break
        if (!$confirmKey = $this->getFirstMatchedKey("forms.base.field_confirmed")) {
            return;
        }

        $sourceFieldName = str_replace('_confirmation', '', $fieldName);
        $sourceRelativeKey = $this->relativeKey($form, $form->get($sourceFieldName), $property);

        // If the source field is not named break
        if (!$sourceTitle = $this->getFirstMatch($sourceRelativeKey)) {
            return;
        }

        return $this->lang->get($confirmKey, ['attribute'=>$sourceTitle]);


    }
}