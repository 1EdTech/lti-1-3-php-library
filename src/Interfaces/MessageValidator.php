<?php
namespace LTI\MessageValidator;

interface MessageValidator
{
    public function validate($jwt_body);
    public function canValidate($jwt_body);
}
