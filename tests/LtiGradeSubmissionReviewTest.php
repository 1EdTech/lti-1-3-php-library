<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\LtiGradeSubmissionReview;

class LtiGradeSubmissionReviewTest extends TestCase
{
    public function setUp(): void
    {
        $this->gradeReview = new LtiGradeSubmissionReview;
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiGradeSubmissionReview::class, $this->gradeReview);
    }

    public function testItGetsReviewableStatus()
    {
        $result = $this->gradeReview->getReviewableStatus();

        $this->assertNull($result);
    }

    public function testItSetsReviewableStatus()
    {
        $expected = 'expected';

        $this->gradeReview->setReviewableStatus($expected);

        $this->assertEquals($expected, $this->gradeReview->getReviewableStatus());
    }

    public function testItGetsLabel()
    {
        $result = $this->gradeReview->getLabel();

        $this->assertNull($result);
    }

    public function testItSetsLabel()
    {
        $expected = 'expected';

        $this->gradeReview->setLabel($expected);

        $this->assertEquals($expected, $this->gradeReview->getLabel());
    }

    public function testItGetsUrl()
    {
        $result = $this->gradeReview->getUrl();

        $this->assertNull($result);
    }

    public function testItSetsUrl()
    {
        $expected = 'expected';

        $this->gradeReview->setUrl($expected);

        $this->assertEquals($expected, $this->gradeReview->getUrl());
    }

    public function testItGetsCustom()
    {
        $result = $this->gradeReview->getCustom();

        $this->assertNull($result);
    }

    public function testItSetsCustom()
    {
        $expected = 'expected';

        $this->gradeReview->setCustom($expected);

        $this->assertEquals($expected, $this->gradeReview->getCustom());
    }

    public function testItCastsFullObjectToString()
    {
        $expected = [
            "reviewableStatus" => 'ReviewableStatus',
            "label" => 'Label',
            "url" => 'Url',
            "custom" => 'Custom',
        ];

        $this->gradeReview->setReviewableStatus($expected['reviewableStatus']);
        $this->gradeReview->setLabel($expected['label']);
        $this->gradeReview->setUrl($expected['url']);
        $this->gradeReview->setCustom($expected['custom']);

        $this->assertEquals(json_encode($expected), (string) $this->gradeReview);
    }

    public function testItCastsEmptyObjectToString()
    {
        $this->assertEquals('[]', (string) $this->gradeReview);
    }
}
