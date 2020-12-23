<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiGradeSubmissionReview;

class LtiGradeSubmissionReviewTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiGradeSubmissionReview();

        $this->assertInstanceOf(LtiGradeSubmissionReview::class, $jwks);
    }
}
