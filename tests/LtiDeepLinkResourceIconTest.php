<?php

namespace Tests;

use Packback\Lti1p3\LtiDeepLinkResourceIcon;

class LtiDeepLinkResourceIconTest extends TestCase
{
    public function setUp(): void
    {
        $this->deepLinkResourceIcon = new LtiDeepLinkResourceIcon();
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiDeepLinkResourceIcon::class, $this->deepLinkResourceIcon);
    }

    public function testItCreatesANewInstance()
    {
        $deepLinkResource = LtiDeepLinkResourceIcon::new();

        $this->assertInstanceOf(LtiDeepLinkResourceIcon::class, $deepLinkResource);
    }

    public function testItGetsUrl()
    {
        $result = $this->deepLinkResourceIcon->getUrl();

        $this->assertEquals(null, $result);
    }

    public function testItSetsUrl()
    {
        $expected = 'expected';

        $this->deepLinkResourceIcon->setUrl($expected);

        $this->assertEquals($expected, $this->deepLinkResourceIcon->getUrl());
    }

    public function testItGetsWidth()
    {
        $result = $this->deepLinkResourceIcon->getWidth();

        $this->assertEquals(null, $result);
    }

    public function testItSetsWidth()
    {
        $expected = 300;

        $this->deepLinkResourceIcon->setWidth($expected);

        $this->assertEquals($expected, $this->deepLinkResourceIcon->getWidth());
    }

    public function testItGetsHeight()
    {
        $result = $this->deepLinkResourceIcon->getHeight();

        $this->assertEquals(null, $result);
    }

    public function testItSetsHeight()
    {
        $expected = 400;

        $this->deepLinkResourceIcon->setHeight($expected);

        $this->assertEquals($expected, $this->deepLinkResourceIcon->getHeight());
    }

    public function testItCastsToArray()
    {
        $expected = [
            'url' => 'https://example.com/icon.png',
            'width' => 100,
            'height' => 200,
        ];

        $this->deepLinkResourceIcon->setUrl($expected['url']);
        $this->deepLinkResourceIcon->setWidth($expected['width']);
        $this->deepLinkResourceIcon->setHeight($expected['height']);

        $result = $this->deepLinkResourceIcon->toArray();

        $this->assertEquals($expected, $result);
    }
}
