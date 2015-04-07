<?php namespace FormObject\Support\Laravel\Http;

use Illuminate\Http\Request;

use FormObject\Http\RequestProviderInterface;

class InputRequestProvider implements RequestProviderInterface{

    /**
     * @var \Illuminate\Http\Request
     **/
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequestAsArray($method)
    {

        if($old = $this->request->old()){
            $data = $old;
        }
        else{
            $data = $this->request->all();
        }

        return $data;

     }

}