<?php namespace Tests\MessageValidator;

use PHPUnit\Framework\TestCase;

use LTI\ResourceMessageValidator;

class ResourceMessageValidatorTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new ResourceMessageValidator();

        $this->assertInstanceOf(ResourceMessageValidator::class, $jwks);
    }
}
