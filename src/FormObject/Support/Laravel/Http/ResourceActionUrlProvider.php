<?php namespace FormObject\Support\Laravel\Http;

use URL;
use Route;

use FormObject\Form;
use FormObject\Http\ActionUrlProviderInterface;

class ResourceActionUrlProvider implements ActionUrlProviderInterface{

    /**
     * This method returns a default action where to send the form if none was
     * excplicit setted via Form::setAction()
     *
     * @param \FormObject\Form $form
     * @return string
     **/
    public function setActionUrl(Form $form){

        $current = explode('/', rtrim(URL::current(),'/'));
        $lastSegment = array_pop($current);

        if(in_array($lastSegment, ['create','edit'])){

            $parentUrl = implode('/',$current);

            $form->setAction($parentUrl);

        }

        if($lastSegment == 'create'){
            $form->setVerb('post');
        }
        elseif($lastSegment == 'edit'){
            $form->setVerb('put');
        }

    }

    /**
     * This method returns a true if this provider is sure to be the right one
     * assign an action to the form
     *
     * @param \FormObject\Form $form
     * @return bool
     **/
    public function matches(Form $form){
        if($this->isResourceRoute()){
            return TRUE;
        }
    }

    protected function isResourceRoute(){

        if($routeName = Route::currentRouteName()){
            if(ends_with($routeName,['.create', '.edit'])){
                return TRUE;
            }
        }

        return FALSE;

    }

}