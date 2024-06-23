<?php namespace FormObject\Support\Laravel\Http;

use XType\Casting\Contracts\InputCaster as InputCasterContract;
use Collection\NestedArray;

class InputCaster implements InputCasterContract
{

    protected $removeConfirmations = true;

    /**
     * {@inheritdoc}
     *
     * @param array $input
     * @param array $metadata (optional)
     * @return array
     **/
    public function castInput(array $input, array $metadata=[])
    {

        $filtered = [];

        foreach ($input as $key=>$value) {

            // tokens, _method...
            if (str_starts_with($key,'_')) {
                continue;
            }

            // actions
            if (str_contains($key, '-')) {
                continue;
            }

            if ($this->removeConfirmations && str_ends_with($key, '_confirmation')) {
                continue;
            }

            $filtered[$key] = $value;
        }

        return NestedArray::toNested($filtered, '__');

    }

    public function withConfirmations($with=true)
    {
        $this->removeConfirmations = !$with;
        return $this;
    }

}