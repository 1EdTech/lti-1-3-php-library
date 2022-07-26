<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\LtiDeepLinkResource;
use Packback\Lti1p3\LtiDeepLinkResourceIcon;
use Packback\Lti1p3\LtiLineitem;

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

    public function testItGetsTitle()
    {
        $result = $this->deepLinkResource->getTitle();

        $this->assertNull($result);
    }

    public function testItSetsTitle()
    {
        $expected = 'expected';

        $this->deepLinkResource->setTitle($expected);

        $this->assertEquals($expected, $this->deepLinkResource->getTitle());
    }

    public function testItGetsText()
    {
        $result = $this->deepLinkResource->getText();

        $this->assertNull($result);
    }

    public function testItSetsText()
    {
        $expected = 'expected';

        $this->deepLinkResource->setText($expected);

        $this->assertEquals($expected, $this->deepLinkResource->getText());
    }

    public function testItGetsUrl()
    {
        $result = $this->deepLinkResource->getUrl();

        $this->assertNull($result);
    }

    public function testItSetsUrl()
    {
        $expected = 'expected';

        $this->deepLinkResource->setUrl($expected);

        $this->assertEquals($expected, $this->deepLinkResource->getUrl());
    }

    public function testItGetsLineitem()
    {
        $result = $this->deepLinkResource->getLineitem();

        $this->assertNull($result);
    }

    public function testItSetsLineitem()
    {
        $expected = Mockery::mock(LtiLineitem::class);

        $this->deepLinkResource->setLineitem($expected);

        $this->assertEquals($expected, $this->deepLinkResource->getLineitem());
    }

    public function testItGetsIcon()
    {
        $result = $this->deepLinkResource->getIcon();

        $this->assertNull($result);
    }

    public function testItSetsIcon()
    {
        $expected = Mockery::mock(LtiDeepLinkResourceIcon::class);

        $this->deepLinkResource->setIcon($expected);

        $this->assertEquals($expected, $this->deepLinkResource->getIcon());
    }

    public function testItGetsThumbnail()
    {
        $result = $this->deepLinkResource->getThumbnail();

        $this->assertNull($result);
    }

    public function testItSetsThumbnail()
    {
        $expected = Mockery::mock(LtiDeepLinkResourceIcon::class);

        $this->deepLinkResource->setThumbnail($expected);

        $this->assertEquals($expected, $this->deepLinkResource->getThumbnail());
    }

    public function testItGetsCustomParams()
    {
        $result = $this->deepLinkResource->getCustomParams();

        $this->assertEquals([], $result);
    }

    public function testItSetsCustomParams()
    {
        $expected = 'expected';

        $this->deepLinkResource->setCustomParams($expected);

        $this->assertEquals($expected, $this->deepLinkResource->getCustomParams());
    }

    public function testItGetsTarget()
    {
        $result = $this->deepLinkResource->getTarget();

        $this->assertEquals('iframe', $result);
    }

    public function testItSetsTarget()
    {
        $expected = 'expected';

        $this->deepLinkResource->setTarget($expected);

        $this->assertEquals($expected, $this->deepLinkResource->getTarget());
    }

    public function testItCastsToArray()
    {
        $icon = (new LtiDeepLinkResourceIcon())
            ->setUrl('a_url')
            ->setWidth(100)
            ->setHeight(200);

        $expected = [
            'type' => 'ltiResourceLink',
            'title' => 'a_title',
            'text' => 'a_text',
            'url' => 'a_url',
            'icon' => [
                'url' => $icon->getUrl(),
                'width' => $icon->getWidth(),
                'height' => $icon->getHeight(),
            ],
            'thumbnail' => [
                'url' => $icon->getUrl(),
                'width' => $icon->getWidth(),
                'height' => $icon->getHeight(),
            ],
            'presentation' => [
                'documentTarget' => 'iframe',
            ],
            'lineItem' => [
                'scoreMaximum' => 80,
                'label' => 'lineitem_label',
            ],
        ];

        $lineitem = Mockery::mock(LtiLineitem::class);
        $lineitem->shouldReceive('getScoreMaximum')
            ->twice()->andReturn($expected['lineItem']['scoreMaximum']);
        $lineitem->shouldReceive('getLabel')
            ->twice()->andReturn($expected['lineItem']['label']);

        $this->deepLinkResource->setTitle($expected['title']);
        $this->deepLinkResource->setText($expected['text']);
        $this->deepLinkResource->setUrl($expected['url']);
        $this->deepLinkResource->setIcon($icon);
        $this->deepLinkResource->setThumbnail($icon);
        $this->deepLinkResource->setLineitem($lineitem);

        $result = $this->deepLinkResource->toArray();

        $this->assertEquals($expected, $result);

        // Test again with custom params
        $expected['custom'] = ['a_key' => 'a_value'];
        $this->deepLinkResource->setCustomParams(['a_key' => 'a_value']);
        $result = $this->deepLinkResource->toArray();
        $this->assertEquals($expected, $result);
    }
}
