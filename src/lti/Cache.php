<?php
namespace IMSGlobal\LTI;

class Cache {

    /** @var array<mixed> $cache */
    private array $cache;

    public function get_launch_data(string $key): mixed {
        $this->load_cache();
        return $this->cache[$key];
    }

    /**
     * @param array<mixed> $jwt_body
     */
    public function cache_launch_data(string $key, array $jwt_body): self {
        $this->cache[$key] = $jwt_body;
        $this->save_cache();
        return $this;
    }

    public function cache_nonce(string $nonce): self {
        $this->cache['nonce'][$nonce] = false;
        $this->save_cache();
        return $this;
    }

    public function check_nonce(string $nonce): bool {
        $this->load_cache();
        if (!isset($this->cache['nonce'][$nonce])) {
            return false;
        }
        if ($this->cache['nonce'][$nonce]) {
            return false;
        }
        $this->cache['nonce'][$nonce] = true;
        $this->save_cache();
        return true;
    }

    private function load_cache(): void {
        $cache = file_get_contents(sys_get_temp_dir() . '/lti_cache.txt');
        if ($cache === false) {
            file_put_contents(sys_get_temp_dir() . '/lti_cache.txt', '{}');
            $this->cache = [];
            return;
        }
        $data = json_decode($cache, true);
        if ($data === false) {
            throw new \Exception("Failed to decode cache contents.");
        }
        $this->cache = $data;
    }

    private function save_cache(): void {
        file_put_contents(sys_get_temp_dir() . '/lti_cache.txt', json_encode($this->cache));
    }
}
