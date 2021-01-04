<?php
namespace Packback\Lti1p3;

class LtiGradeSubmissionReview
{
    private $reviewable_status;
    private $label;
    private $url;
    private $custom;

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new() {
        return new LtiGradeSubmissionReview();
    }

    public function getReviewableStatus()
    {
        return $this->reviewable_status;
    }

    public function setReviewableStatus($value)
    {
        $this->reviewable_status = $value;
        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($value)
    {
        $this->label = $value;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getCustom()
    {
        return $this->custom;
    }

    public function setCustom($value)
    {
        $this->custom = $value;
        return $this;
    }

    public function __toString()
    {
        return json_encode(array_filter([
            "reviewableStatus" => $this->reviewable_status,
            "label" => $this->label,
            "url" => $this->url,
            "custom" => $this->custom,
        ]));
    }
}
?>
