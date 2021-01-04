<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\LtiGrade;

class LtiGradeTest extends TestCase
{
    public function setUp(): void
    {
        $this->grade = new LtiGrade;
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiGrade::class, $this->grade);
    }

    public function testItCreatesANewInstance()
    {
        $grade = LtiGrade::new();

        $this->assertInstanceOf(LtiGrade::class, $grade);
    }

    public function testItGetsScoreGiven()
    {
        $result = $this->grade->getScoreGiven();

        $this->assertNull($result);
    }

    public function testItSetsScoreGiven()
    {
        $expected = 'expected';

        $this->grade->setScoreGiven($expected);

        $this->assertEquals($expected, $this->grade->getScoreGiven());
    }

    public function testItGetsScoreMaximum()
    {
        $result = $this->grade->getScoreMaximum();

        $this->assertNull($result);
    }

    public function testItSetsScoreMaximum()
    {
        $expected = 'expected';

        $this->grade->setScoreMaximum($expected);

        $this->assertEquals($expected, $this->grade->getScoreMaximum());
    }

    public function testItGetsComment()
    {
        $result = $this->grade->getComment();

        $this->assertNull($result);
    }

    public function testItSetsComment()
    {
        $expected = 'expected';

        $this->grade->setComment($expected);

        $this->assertEquals($expected, $this->grade->getComment());
    }

    public function testItGetsActivityProgress()
    {
        $result = $this->grade->getActivityProgress();

        $this->assertNull($result);
    }

    public function testItSetsActivityProgress()
    {
        $expected = 'expected';

        $this->grade->setActivityProgress($expected);

        $this->assertEquals($expected, $this->grade->getActivityProgress());
    }

    public function testItGetsGradingProgress()
    {
        $result = $this->grade->getGradingProgress();

        $this->assertNull($result);
    }

    public function testItSetsGradingProgress()
    {
        $expected = 'expected';

        $this->grade->setGradingProgress($expected);

        $this->assertEquals($expected, $this->grade->getGradingProgress());
    }

    public function testItGetsTimestamp()
    {
        $result = $this->grade->getTimestamp();

        $this->assertNull($result);
    }

    public function testItSetsTimestamp()
    {
        $expected = 'expected';

        $this->grade->setTimestamp($expected);

        $this->assertEquals($expected, $this->grade->getTimestamp());
    }

    public function testItGetsUserId()
    {
        $result = $this->grade->getUserId();

        $this->assertNull($result);
    }

    public function testItSetsUserId()
    {
        $expected = 'expected';

        $this->grade->setUserId($expected);

        $this->assertEquals($expected, $this->grade->getUserId());
    }

    public function testItGetsSubmissionReview()
    {
        $result = $this->grade->getSubmissionReview();

        $this->assertNull($result);
    }

    public function testItSetsSubmissionReview()
    {
        $expected = 'expected';

        $this->grade->setSubmissionReview($expected);

        $this->assertEquals($expected, $this->grade->getSubmissionReview());
    }
}
