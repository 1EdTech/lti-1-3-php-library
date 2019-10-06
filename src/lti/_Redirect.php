<?php
namespace IMSGlobal\LTI;

class Redirect {

    private $location;

    public function __construct($location) {
        $this->location = $location;
    }

    public function do_redirect() {
        header('Location: ' . $this->location, true, 302);
        die;
    }

    public function do_js_redirect() {
        echo "<script>window.location='{$this->location}';</script>";
    }

    public function get_redirect_url() {
        return $this->location;
    }
}

?>