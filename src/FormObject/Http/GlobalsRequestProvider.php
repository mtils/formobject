<?php namespace FormObject\Http;

class GlobalsRequestProvider implements RequestProviderInterface{

    public function getRequestAsArray($method){

        if($method == 'post'){
            return $_POST;
        }
        elseif($method == 'get'){
            return $_GET;
        }
        return $_REQUEST;

    }

}