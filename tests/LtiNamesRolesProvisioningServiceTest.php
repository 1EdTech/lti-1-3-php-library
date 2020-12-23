<?php namespace Tests;

use PHPUnit\Framework\TestCase;
use Mockery;

use Packback\Lti1p3\Interfaces\LtiServiceConnectorInterface;
use Packback\Lti1p3\LtiNamesRolesProvisioningService;

class LtiNamesRolesProvisioningServiceTest extends TestCase
{
    public function testItInstantiates()
    {
        $connector = Mockery::mock(LtiServiceConnectorInterface::class);

        $nrps = new LtiNamesRolesProvisioningService($connector, []);

        $this->assertInstanceOf(LtiNamesRolesProvisioningService::class, $nrps);
    }

    /**
     * TODO: Finish testing
     */
}
