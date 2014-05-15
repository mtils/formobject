<?php namespace FormObject\Validator;

// use FormObject\Validator\FactoryInterface;
// use \UnexpectedValueException;
use FormObject\FormItem;
use FormObject\Field;

class SimpleFactory implements FactoryInterface{

    public function createForField(FormItem $item){
        if($item instanceof \FormObject\Field\TextField){
            return $this->forTextField($item);
        }
        elseif($item instanceof \FormObject\Field\HiddenField){
            return $this->forTextField($item);
        }
        elseif($item instanceof \FormObject\Field\BooleanField){
            return $this->forBooleanField($item);
        }
        elseif($item instanceof \FormObject\Field\BooleanField){
            return $this->forBooleanField($item);
        }
        elseif($item instanceof \FormObject\Field\SelectOneField){
            return $this->forSelectOneField($item);
        }
        elseif($item instanceof \FormObject\Field\SelectManyField){
            return $this->forSelectManyField($item);
        }
        else{
            throw new \UnexpectedValueException("Cannot create validator for field ".$item->getClassName());
        }
    }

    public function createForHiddenField($item){
        $validator = new TextValidator();
        $validator->required = $item->isRequired();
        return $validator;
    }

    public function forTextField($item){

        $validator = new TextValidator();
        $validator->required = $item->isRequired();

        if($item->minLength){
            $validator->minLength = $item->minLength;
        }
        if($item->maxLength){
            $validator->maxLength = $item->maxLength;
        }
        if($item->allowHtml){
            $validator->allowHtml = TRUE;
        }
        return $validator;
    }

    public function forBooleanField($item){
        $validator = new BooleanValidator();
        $validator->mustBeTrue = $item->mustBeTrue;
        return $validator;
    }

    public function forSelectOneField($item){
        $validator = new InListValidator();
        $validator->required = $item->isRequired();
        $validator->allowedValues = array_keys($item->getIterator()->toArray());
        return $validator;
    }

    public function forSelectManyField($item){
        $validator = new MultipleInListValidator();
        $validator->required = $item->isRequired();
        $validator->allowedValues = array_keys($item->getIterator()->toArray());
        return $validator;
    }
}