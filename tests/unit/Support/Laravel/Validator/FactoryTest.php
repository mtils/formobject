<?php

namespace FormObject\Test\Support\Laravel\Validator;

use FormObject\Validator\SimpleValidator;
use Illuminate\Contracts\Translation\Translator;
use PHPUnit\Framework\TestCase;
use FormObject\Support\Laravel\Validator\Factory;
use FormObject\Form;
use FormObject\Validator\ValidatorInterface;
use Illuminate\Validation\Validator as LaravelValidator;

class FactoryTest extends TestCase
{

    public function testCreateValidatorWithValidationRulesProperty(){

        $form = new class extends Form {
            public $validationRules = [
                'name' => 'required',
            ];
        };
        $factory = new Factory();

        $this->assertInstanceOf(
            ValidatorInterface::class,
            $factory->createValidator($form)
        );
    }

    public function testCreateValidatorWithValidationRulesMethod(){

        $form = new class extends Form {
            public function validationRules()
            {
                return ['name' => 'required'];
            }
        };
        $factory = new Factory();

        $this->assertInstanceOf(
            ValidatorInterface::class,
            $factory->createValidator($form)
        );
    }

    public function testCreateValidatorWithExistingLaravelValidator(){

        $translator = $this->createMock(Translator::class);
        $laravelValidator = new LaravelValidator($translator, [], ['name' => 'required']);
        $form = new class($laravelValidator) extends Form {
            protected LaravelValidator $laravelValidator;
            public function __construct(LaravelValidator $validator)
            {
                $this->laravelValidator = $validator;
            }

            public function createValidator()
            {
                return $this->laravelValidator;
            }
        };
        $factory = new Factory();

        $this->assertInstanceOf(
            ValidatorInterface::class,
            $factory->createValidator($form)
        );
        $this->assertSame($laravelValidator, $factory->createValidator($form)->getSrcValidator());
    }

}