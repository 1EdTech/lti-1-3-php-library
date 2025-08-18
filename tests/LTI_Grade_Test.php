<?PHP
namespace IMSGlobal\LTI;

class LTI_Grade_Test extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $grade = LTI_Grade::new();

        $grade
            ->set_score_given("90")
            ->set_score_maximum("100")
            ->set_comment("boo!")
            ->set_activity_progress("activity complete")
            ->set_grading_progress("grading complete")
            ->set_timestamp("2025-01-01T12:00:00")
            ->set_user_id("1000")
            ->set_submission_review("review complete");

        $this->assertEquals(
            "90",
            $grade->get_score_given()
        );
        $this->assertEquals(
            "100",
            $grade->get_score_maximum()
        );
        $this->assertEquals(
            "boo!",
            $grade->get_comment()
        );
        $this->assertEquals(
            "activity complete",
            $grade->get_activity_progress()
        );
        $this->assertEquals(
            "grading complete",
            $grade->get_grading_progress()
        );
        $this->assertEquals(
            "2025-01-01T12:00:00",
            $grade->get_timestamp()
        );
        $this->assertEquals(
            "1000",
            $grade->get_user_id()
        );
        $this->assertEquals(
            "review complete",
            $grade->get_submission_review()
        );

        $ExpectedString =
            '{"scoreGiven":"90","scoreMaximum":"100","comment":"boo!",'
            .'"activityProgress":"activity complete","gradingProgress":"grading complete",'
            .'"timestamp":"2025-01-01T12:00:00","userId":"1000",'
            .'"submissionReview":"review complete"}';
        $this->assertEquals((string)$grade, $ExpectedString);
    }
}
