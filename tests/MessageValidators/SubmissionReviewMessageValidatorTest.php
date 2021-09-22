<?php

namespace Tests\MessageValidators;

use Packback\Lti1p3\MessageValidators\SubmissionReviewMessageValidator;
use Tests\TestCase;

class SubmissionReviewMessageValidatorTest extends TestCase
{
    public function testItInstantiates()
    {
        $validator = new SubmissionReviewMessageValidator([]);

        $this->assertInstanceOf(SubmissionReviewMessageValidator::class, $validator);
    }
}
