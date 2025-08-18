<?PHP
namespace IMSGlobal\LTI;

class LTI_Lineitem_Test extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $item = LTI_Lineitem::new();

        $item
            ->set_id("X-ID-X")
            ->set_label("X-LABEL-X")
            ->set_score_maximum("100")
            ->set_resource_id("X-RESOURCE-ID-X")
            ->set_tag("X-TAG-X")
            ->set_start_date_time("2025-01-01T12:00:00")
            ->set_end_date_time("2025-01-02T12:00:00");

        $this->assertEquals(
            "X-ID-X",
            $item->get_id()
        );
        $this->assertEquals(
            "X-LABEL-X",
            $item->get_label()
        );
        $this->assertEquals(
            "100",
            $item->get_score_maximum()
        );
        $this->assertEquals(
            "X-RESOURCE-ID-X",
            $item->get_resource_id()
        );
        $this->assertEquals(
            "X-TAG-X",
            $item->get_tag()
        );
        $this->assertEquals(
            "2025-01-01T12:00:00",
            $item->get_start_date_time()
        );
        $this->assertEquals(
            "2025-01-02T12:00:00",
            $item->get_end_date_time()
        );

        $ExpectedString =
            '{"id":"X-ID-X","scoreMaximum":"100","label":"X-LABEL-X",'
            .'"resourceId":"X-RESOURCE-ID-X","tag":"X-TAG-X",'
            .'"startDateTime":"2025-01-01T12:00:00",'
            .'"endDateTime":"2025-01-02T12:00:00"}';
        $this->assertEquals($ExpectedString, (string)$item);
    }
}
