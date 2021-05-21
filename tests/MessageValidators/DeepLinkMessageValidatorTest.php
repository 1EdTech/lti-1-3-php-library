<?php

namespace Tests\MessageValidators;

use Packback\Lti1p3\MessageValidators\DeepLinkMessageValidator;
use PHPUnit\Framework\TestCase;

class DeepLinkMessageValidatorTest extends TestCase
{
    public function testItInstantiates()
    {
        $validator = new DeepLinkMessageValidator([]);

        $this->assertInstanceOf(DeepLinkMessageValidator::class, $validator);
    }
}
