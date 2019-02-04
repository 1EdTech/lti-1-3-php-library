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
        echo "<button onclick=\"window.location='{$this->location}'\">Go!</button>";
    }

    public function get_redirect_url() {
        return $this->location;
    }
}

?>