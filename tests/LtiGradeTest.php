<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use Packback\Lti1p3\LtiGrade;

class LtiGradeTest extends TestCase
{

    public function testItInstantiates()
    {
        $grade = new LtiGrade();

        $this->assertInstanceOf(LtiGrade::class, $grade);
    }

    /**
     * TODO: Finish testing
     */
}
