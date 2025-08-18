<?php
namespace IMSGlobal\LTI;

class Cookie {
    /**
     * @return string|false
     */
    public function get_cookie(string $name) {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        // Look for backup cookie if same site is not supported by the user's browser.
        if (isset($_COOKIE["LEGACY_" . $name])) {
            return $_COOKIE["LEGACY_" . $name];
        }
        return false;
    }

    /**
     * @param array<mixed> $options
     */
    public function set_cookie(string $name, string $value, int $exp = 3600, array $options = []): self {
        $cookie_options = [
            'expires' => time() + $exp
        ];

        // SameSite none and secure will be required for tools to work inside iframes
        $same_site_options = [
            'samesite' => 'None',
            'secure' => true
        ];

        setcookie($name, $value, array_merge($cookie_options, $same_site_options, $options));

        // Set a second fallback cookie in the event that "SameSite" is not supported
        setcookie("LEGACY_" . $name, $value, array_merge($cookie_options, $options));
        return $this;
    }
}
