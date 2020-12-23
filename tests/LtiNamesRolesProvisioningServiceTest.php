<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiNamesRolesProvisioningService;

class LtiNamesRolesProvisioningServiceTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiNamesRolesProvisioningService();

        $this->assertInstanceOf(LtiNamesRolesProvisioningService::class, $jwks);
    }
}
