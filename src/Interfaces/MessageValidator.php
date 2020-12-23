<?php
namespace Packback\Lti1p3\Interfaces;

interface MessageValidator
{
    public function validate(array $jwt_body);
    public function canValidate(array $jwt_body);
}
