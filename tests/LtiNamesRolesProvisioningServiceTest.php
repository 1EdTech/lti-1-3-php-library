<?php namespace Tests;

use PHPUnit\Framework\TestCase;
use Mockery;

use LTI\Interfaces\LtiServiceConnectorInterface;
use LTI\LtiNamesRolesProvisioningService;

class LtiNamesRolesProvisioningServiceTest extends TestCase
{
    public function testItInstantiates()
    {
        $connector = Mockery::mock(LtiServiceConnectorInterface::class);

        $nrps = new LtiNamesRolesProvisioningService($connector, []);

        $this->assertInstanceOf(LtiNamesRolesProvisioningService::class, $nrps);
    }
}
