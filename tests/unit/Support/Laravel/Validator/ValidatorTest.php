<?php
/**
 *
 * Created by mtils on 09.05.2024 at 12:37.
 **/

namespace FormObject\Test\Support\Laravel\Validator;


use FormObject\Field;
use FormObject\Form;
use FormObject\Support\Laravel\Validator\Validator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Validator as LaravelValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function var_dump;

class ValidatorTest extends TestCase
{

    #[Test] public function validateForwardsToLaravelIfPreviouslyAssigned()
    {
        $form = new Form();
        $validator = new Validator($form);
        $translator = $this->createMock(Translator::class);
        $rules = ['foo' => 'min:3'];
        $validator->setRules($rules);
        $laravelValidator = new LaravelValidator($translator, [], []);
        $validator->setSrcValidator($laravelValidator);

        $this->assertTrue($validator->validate(['foo' => 'bar']));
    }

    #[Test] public function validateForwardsToLaravelValidatorNotAssigned()
    {
        $form = new Form();
        $validator = new Validator($form);

        $rules = ['foo' => 'min:3'];
        $data = ['foo' => 'bar'];
        $validator->setRules($rules);


        Validator::setFactoryCallback(function($passedData, $passedRules) use ($rules, $data) {
            $translator = $this->createMock(Translator::class);
            $this->assertEquals([], $passedData);
            $this->assertEquals($rules, $passedRules);
            return new LaravelValidator($translator, [], []);
        });

        $this->assertTrue($validator->validate($data));

        $this->assertFalse($validator->validate(['foo' => 'ba']));

    }

    #[Test] public function getMessagesConvertsToAttributes()
    {
        $form = new Form();
        $form->push(new Field('foo', 'Foo Field'));
        $form->fakeSubmit();
        $validator = new Validator($form);
        $translator = $this->createMock(Translator::class);
        $rules = ['foo' => 'min:3'];
        $validator->setRules($rules);
        $message = 'broken';
        $laravelValidator = new LaravelValidator($translator, [], [], [
            'min' => $message
        ]);
        $validator->setSrcValidator($laravelValidator);

        $this->assertFalse($validator->validate(['foo' => 'ba']));

        Validator::setViewErrorCallback(function() use ($laravelValidator) {
            return $laravelValidator->messages();
        });

        $this->assertEquals([$message], $validator->getMessages());

        $this->assertEquals([$message], $validator->getMessages('foo'));

    }


}