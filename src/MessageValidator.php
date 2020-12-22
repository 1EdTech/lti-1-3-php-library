<?php
namespace LTI;

interface MessageValidator {
    public function validate($jwt_body);
    public function can_validate($jwt_body);
}
