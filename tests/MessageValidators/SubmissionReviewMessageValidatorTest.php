<?php namespace Tests\MessageValidator;

use PHPUnit\Framework\TestCase;

use LTI\SubmissionReviewMessageValidator;

class SubmissionReviewMessageValidatorTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new SubmissionReviewMessageValidator();

        $this->assertInstanceOf(SubmissionReviewMessageValidator::class, $jwks);
    }
}
