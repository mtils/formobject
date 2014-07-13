<?php namespace FormObject\Support\Laravel;

use DomainException;
use FormObject\Form;
use FormObject\AdapterFactoryInterface;
use FormObject\Field\HiddenField;
use App;
use Illuminate\Validation\Validator;
use URL;
use \Input;
use FormObject\FieldList;

class LaravelForm extends Form{

    protected $sessionToken = '';

    protected $sessionTokenName = '_token';

    protected $appendCsrfToken = FALSE;

    protected $autoFillByRequest = TRUE;

    protected function appendAdditionalFields(FieldList &$fields){
        if($this->appendCsrfToken){
            $app = App::getFacadeRoot();
            $fields->push(HiddenField::create('_token')
                                       ->setValue($app['session.store']->getToken()));
        }
    }

    public function setValidator($validator){

        if(!$validator instanceof Validator){
            throw new DomainException('LaravelForm Validators have to be Illuminate\Validation\Validator not '.get_class($validator));
        }
        $validator->setAttributeNames($this->buildAttributeNames());

        parent::setValidator($validator);
    }

    protected function buildAttributeNames(){
        $attributeNames = array();
        foreach($this->getDataFields() as $field){
            $attributeNames[$field->getName()] = $field->getTitle();
        }
        return $attributeNames;
    }

    public function getAction(){
        if(!$this->action){
            $this->setAction(URL::current());
        }
        return $this->action;
    }
}
