<?PHP
namespace IMSGlobal\LTI;

class TestCache extends \IMSGlobal\LTI\Cache {
    private array $cache;

    public function get_launch_data(string $key): mixed {
        return $this->cache[$key];
    }

    public function cache_launch_data(string $key, array $jwt_body): self {
        $this->cache[$key] = $jwt_body;
        return $this;
    }

    public function cache_nonce(string $nonce): self {
        $this->cache['nonce'][$nonce] = false;
        return $this;
    }

    public function check_nonce(string $nonce): bool {
        if ($nonce == "X-NONCE-X") {
            return true;
        }
        if (!isset($this->cache['nonce'][$nonce])) {
            return false;
        }
        if ($this->cache['nonce'][$nonce]) {
            return false;
        }
        $this->cache['nonce'][$nonce] = true;
        return true;
    }
}
