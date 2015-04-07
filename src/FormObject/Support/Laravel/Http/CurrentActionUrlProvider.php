<?php namespace FormObject\Support\Laravel\Http;

use FormObject\Form;
use FormObject\Http\ActionUrlProviderInterface;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;

class CurrentActionUrlProvider implements ActionUrlProviderInterface{

    /**
     * @var \Illuminate\Contracts\Routing\UrlGenerator
    **/
    protected $urlGenerator;

    /**
     * @var \Illuminate\Http\Request
    **/
    protected $request;

    /**
     * @param \Illuminate\Contracts\Routing\UrlGenerator $generator
     * @param \Illuminate\Http\Request $request
     **/
    public function __construct(UrlGenerator $generator, Request $request)
    {
        $this->urlGenerator = $generator;
        $this->request = $request;
    }

    /**
     * This method returns a default action where to send the form if none was
     * excplicit setted via Form::setAction()
     *
     * @param \FormObject\Form $form
     * @return string
     **/
    public function setActionUrl(Form $form)
    {
        $form->setAction($this->currentUrl());
    }

    /**
     * This method returns a true if this provider is sure to be the right one
     * assign an action to the form
     *
     * @param \FormObject\Form $form
     * @return bool
     **/
    public function matches(Form $form)
    {
        return TRUE;
    }

    /**
     * Returns the current Url
     *
     **/
    public function currentUrl(){
        return $this->urlGenerator->to($this->request->getPathInfo());
    }

}