<?php

namespace Packback\Lti1p3\Helpers;

class Helpers
{
    /**
     * @param $value
     * @return bool
     */
    public function checkIfNullValue($value): bool
  {
    return !is_null($value);
  }
}
