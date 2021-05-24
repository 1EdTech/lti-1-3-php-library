<?php

namespace Tests\MessageValidators;

use Packback\Lti1p3\MessageValidators\ResourceMessageValidator;
use PHPUnit\Framework\TestCase;

class ResourceMessageValidatorTest extends TestCase
{
    public function testItInstantiates()
    {
        $validator = new ResourceMessageValidator([]);

        $this->assertInstanceOf(ResourceMessageValidator::class, $validator);
    }
}
