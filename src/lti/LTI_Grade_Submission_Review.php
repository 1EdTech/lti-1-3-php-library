<?php
namespace IMSGlobal\LTI;

class LTI_Grade_Submission_Review {
    private string $reviewable_status = "";
    private string $label = "";
    private string $url = "";
    private string $custom = "";

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(): self {
        return new LTI_Grade_Submission_Review();
    }

    public function get_reviewable_status(): string {
        return $this->reviewable_status;
    }

    public function set_reviewable_status(string $value): self {
        $this->reviewable_status = $value;
        return $this;
    }

    public function get_label(): string {
        return $this->label;
    }

    public function set_label(string $value): self {
        $this->label = $value;
        return $this;
    }

    public function get_url(): string {
        return $this->url;
    }

    public function set_url(string $url): self {
        $this->url = $url;
        return $this;
    }

    public function get_custom(): string {
        return $this->custom;
    }

    public function set_custom(string $value):self {
        $this->custom = $value;
        return $this;
    }

    public function __toString(): string {
        return (string)json_encode(array_filter([
            "reviewableStatus" => $this->reviewable_status,
            "label" => $this->label,
            "url" => $this->url,
            "custom" => $this->custom,
        ]));
    }
}
