<?php

namespace FormObject\Validator;

interface ValidatorInterface{

    public function isValid($value);

    public function getMessages();

}