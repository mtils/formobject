<?php

namespace FormObject;

use FormObject\Validator\FactoryInterface;

final class Registry{

    private static array $store = [];

    private function __construct(){
    }

    public static function getRenderer(){
        if(!isset(self::$store['renderer'])){
            self::$store['renderer'] = new Renderer\PhpRenderer();
        }
        return self::$store['renderer'];
    }

    public static function setRenderer(Renderer\RendererInterFace $renderer){
        self::$store['renderer'] = $renderer;
    }

    /**
    * @brief Returns the factory for validators
    * @return FactoryInterface
    */
    public static function getValidatorFactory()
    {
        if(!isset(self::$store['validatorFactory'])){
            self::$store['validatorFactory'] = new Validator\SimpleFactory;
        }
        return self::$store['validatorFactory'];
    }

    public static function setValidatorFactory(FactoryInterface $factory){
        self::$store['validatorFactory'] = $factory;
    }

    public static function getRedirector(){
        return self::$store['redirector'];
    }

    public static function setRedirector($redirector){
        self::$store['redirector'] = $redirector;
    }

    public static function __callStatic($name, $arguments){

    }
}