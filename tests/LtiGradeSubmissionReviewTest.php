<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\LtiGradeSubmissionReview;

class LtiGradeSubmissionReviewTest extends TestCase
{

    public function testItInstantiates()
    {
        $gradeReview = new LtiGradeSubmissionReview();

        $this->assertInstanceOf(LtiGradeSubmissionReview::class, $gradeReview);
    }
}
