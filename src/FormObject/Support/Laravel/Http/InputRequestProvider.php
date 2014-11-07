<?php namespace FormObject\Support\Laravel\Http;

use Input;

use FormObject\Http\RequestProviderInterface;

class InputRequestProvider implements RequestProviderInterface{

     public function getRequestAsArray($method){

        if($old = Input::old()){
            $data = $old;
        }
        else{
            $data = Input::all();
        }

        return $data;

     }

}