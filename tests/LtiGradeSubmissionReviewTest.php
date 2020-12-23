<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiGradeSubmissionReview;

class LtiGradeSubmissionReviewTest extends TestCase
{

    public function testItInstantiates()
    {
        $gradeReview = new LtiGradeSubmissionReview();

        $this->assertInstanceOf(LtiGradeSubmissionReview::class, $gradeReview);
    }
}
