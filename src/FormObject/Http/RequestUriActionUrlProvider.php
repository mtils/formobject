<?php namespace FormObject\Http;

use FormObject\Form;

class RequestUriActionUrlProvider implements ActionUrlProviderInterface{

    /**
     * This method returns a true if this provider is sure to be the right one
     * assign an action to the form
     *
     * @param \FormObject\Form $form
     * @return bool
     **/
    public function matches(Form $form){
        return TRUE;
    }

    /**
     * This method returns a default action where to send the form if none was
     * excplicit setted via Form::setAction()
     *
     * @param \FormObject\Form $form
     * @return string
     **/
    public function setActionUrl(Form $form){
        return $form->setAction(strtok($_SERVER["REQUEST_URI"],'?'));
    }

}