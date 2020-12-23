<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\LtiDeepLinkResource;

class LtiDeepLinkResourceTest extends TestCase
{
    public function setUp(): void
    {
        $this->deepLinkResource = new LtiDeepLinkResource();
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiDeepLinkResource::class, $this->deepLinkResource);
    }

    public function testItCreatesANewInstance()
    {
        $deepLinkResource = LtiDeepLinkResource::new();

        $this->assertInstanceOf(LtiDeepLinkResource::class, $deepLinkResource);
    }

    public function testItGetsType()
    {
        $result = $this->deepLinkResource->getType();

        $this->assertEquals('ltiResourceLink', $result);
    }

    public function testItSetsType()
    {
        $expected = 'expected';

        $this->deepLinkResource->setType($expected);

        $this->assertEquals($expected, $this->deepLinkResource->getType());
    }

    /**
     * TODO: finish testing
     */
}
