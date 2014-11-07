<?php namespace FormObject\Http;

interface RequestProviderInterface{

    public function getRequestAsArray($method);

}