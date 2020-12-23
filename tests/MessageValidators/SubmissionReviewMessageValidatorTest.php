<?php namespace Tests\MessageValidators;

use PHPUnit\Framework\TestCase;

use LTI\MessageValidators\SubmissionReviewMessageValidator;

class SubmissionReviewMessageValidatorTest extends TestCase
{

    public function testItInstantiates()
    {
        $validator = new SubmissionReviewMessageValidator([]);

        $this->assertInstanceOf(SubmissionReviewMessageValidator::class, $validator);
    }
}
