<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiOidcLogin;

class LtiOidcLoginTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiOidcLogin();

        $this->assertInstanceOf(LtiOidcLogin::class, $jwks);
    }
}
