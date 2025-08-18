<?php
namespace IMSGlobal\LTI;

class LTI_Lineitem {
    private string $id = "";
    private string $score_maximum = "";
    private string $label = "";
    private string $resource_id = "";
    private string $tag = "";
    private string $start_date_time = "";
    private string $end_date_time = "";

    /**
     * @param array<string> $lineitem
     */
    public function __construct(?array $lineitem = null) {
        if (empty($lineitem)) {
            return;
        }
        $this->id = $lineitem["id"];
        $this->score_maximum = $lineitem["scoreMaximum"];
        $this->label = $lineitem["label"];
        $this->resource_id = $lineitem["resourceId"];
        $this->tag = $lineitem["tag"];
        $this->start_date_time = $lineitem["startDateTime"];
        $this->end_date_time = $lineitem["endDateTime"];
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(): self {
        return new LTI_Lineitem();
    }

    public function get_id(): string {
        return $this->id;
    }

    public function set_id(string $value): self {
        $this->id = $value;
        return $this;
    }

    public function get_label(): string {
        return $this->label;
    }

    public function set_label(string $value): self {
        $this->label = $value;
        return $this;
    }

    public function get_score_maximum(): string {
        return $this->score_maximum;
    }

    public function set_score_maximum(string $value): self {
        $this->score_maximum = $value;
        return $this;
    }

    public function get_resource_id(): string {
        return $this->resource_id;
    }

    public function set_resource_id(string $value): self {
        $this->resource_id = $value;
        return $this;
    }

    public function get_tag(): string {
        return $this->tag;
    }

    public function set_tag(string $value): self {
        $this->tag = $value;
        return $this;
    }

    public function get_start_date_time(): string {
        return $this->start_date_time;
    }

    public function set_start_date_time(string $value): self {
        $this->start_date_time = $value;
        return $this;
    }

    public function get_end_date_time(): string {
        return $this->end_date_time;
    }

    public function set_end_date_time(string $value): self {
        $this->end_date_time = $value;
        return $this;
    }

    public function __toString(): string {
        return (string)json_encode(array_filter([
            "id" => $this->id,
            "scoreMaximum" => $this->score_maximum,
            "label" => $this->label,
            "resourceId" => $this->resource_id,
            "tag" => $this->tag,
            "startDateTime" => $this->start_date_time,
            "endDateTime" => $this->end_date_time,
        ]));
    }
}
