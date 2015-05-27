<?php namespace FormObject\Support\Laravel\Http;

use XType\Casting\Contracts\InputCaster as InputCasterContract;
use Collection\NestedArray;

class InputCaster implements InputCasterContract
{

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
            if (starts_with($key,'_')) {
                continue;
            }

            // actions
            if (str_contains($key, '-')) {
                continue;
            }

            if (ends_with($key, '_confirmation')) {
                continue;
            }

            $filtered[$key] = $value;
        }

        return NestedArray::toNested($filtered, '__');

    }

}