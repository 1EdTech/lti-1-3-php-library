<?php namespace Tests;

use PHPUnit\Framework\TestCase;

use LTI\LtiGrade;

class LtiGradeTest extends TestCase
{

    public function testItInstantiates()
    {
        $jwks = new LtiGrade();

        $this->assertInstanceOf(LtiGrade::class, $jwks);
    }
}
