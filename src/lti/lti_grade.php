<?php
namespace IMSGlobal\LTI;

class LTI_Grade {
    private $score_given;
    private $score_maximum;
    private $activity_progress;
    private $grading_progress;
    private $timestamp;
    private $user_id;

    public static function new() {
        return new LTI_Grade();
    }

    public function get_score_given() {
        return $this->score_given;
    }

    public function set_score_given($value) {
        $this->score_given = $value;
        return $this;
    }

    public function get_score_maximum() {
        return $this->score_maximum;
    }

    public function set_score_maximum($value) {
        $this->score_maximum = $value;
        return $this;
    }

    public function get_activity_progress() {
        return $this->activity_progress;
    }

    public function set_activity_progress($value) {
        $this->activity_progress = $value;
        return $this;
    }

    public function get_grading_progress() {
        return $this->grading_progress;
    }

    public function set_grading_progress($value) {
        $this->grading_progress = $value;
        return $this;
    }

    public function get_timestamp() {
        return $this->timestamp;
    }

    public function set_timestamp($value) {
        $this->timestamp = $value;
        return $this;
    }

    public function get_user_id() {
        return $this->user_id;
    }

    public function set_user_id($value) {
        $this->user_id = $value;
        return $this;
    }

    public function __toString() {
        return json_encode([
            "scoreGiven" => $this->score_given,
            "scoreMaximum" => $this->score_maximum,
            "activityProgress" => $this->activity_progress,
            "gradingProgress" => $this->grading_progress,
            "timestamp" => $this->timestamp,
            "userId" => $this->user_id,
        ]);
    }
}
?>