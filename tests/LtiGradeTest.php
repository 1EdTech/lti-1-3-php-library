<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiGrade;

class LtiGradeTest extends TestCase
{

    public function testItInstantiates()
    {
        $grade = new LtiGrade();

        $this->assertInstanceOf(LtiGrade::class, $grade);
    }
}
