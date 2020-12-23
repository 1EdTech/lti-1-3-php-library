<?php namespace Tests\MessageValidator;

use PHPUnit\Framework\TestCase;

use LTI\DeepLinkMessageValidator;

class DeepLinkMessageValidatorTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new DeepLinkMessageValidator();

        $this->assertInstanceOf(DeepLinkMessageValidator::class, $jwks);
    }
}
