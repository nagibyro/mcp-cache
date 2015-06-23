<?php
/**
 * @copyright ©2014 Quicken Loans Inc. All rights reserved. Trade Secret,
 *    Confidential and Proprietary. Any dissemination outside of Quicken Loans
 *    is strictly prohibited.
 */

namespace MCP\Cache;

use MCP\Cache\Item\Item;
use MCP\DataType\Time\Clock;

/**
 * APC In-Memory Cache Implementation
 *
 * Since APC handles expired keys poorly, we've enforced TTL values here. For example, regardless of TTL values, APC
 * will not expunge any keys until the cache reaches the maximum size. It will also only invalidate after a call to
 * fetch() which means it will return an expired key once before expunging it.
 */
class APCCache implements CacheInterface
{
    /**
     * @var \MCP\DataType\Time\Clock
     */
    private $clock;

    /**
     * @var null|int
     */
    private $ttl;

    /**
     * @param \MCP\DataType\Time\Clock $clock
     */
    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
        $this->ttl = null;
    }

    /**
     * Get a cache value by key. When a matching entry cannot be found, null will be returned.
     *
     * @param string $key
     * @return mixed|null
     */
    public function get($key)
    {
        $value = apc_fetch($key, $success);

        if ($success && $value instanceof Item) {
            return $value->data($this->clock->read());
        }

        return null;
    }

    /**
     * Set a value by key in the cache. Returns true on success.
     *
     * @param string $key
     * @param mixed $value Anything not a resource
     * @param int $ttl How long the data should live, in seconds
     * @return boolean
     */
    public function set($key, $value, $ttl = 0)
    {
        $expires = null;
        $ttl = $this->allowedTtl($ttl);

        // already expired, invalidate stored value and don't insert
        if ($ttl < 0) {
            apc_delete($key);
            return true;
        }

        if ($ttl > 0) {
            $expires = $this->clock->read()->modify(sprintf('+%d seconds', $ttl));
        }

        return apc_store($key, new Item($value, $expires), $ttl);
    }

    /**
     * Clear the cache
     *
     * @return bool
     */
    public function clear()
    {
        return apc_clear_cache('user');
    }

    /**
     * Set the maximum ttl in seconds
     *
     * @param $ttl
     */
    public function setMaximumTtl($ttl)
    {
        $this->ttl = (int)$ttl;
    }

    /**
     * Determine the actual TTL
     *
     * @param $ttl
     * @return int
     */
    private function allowedTtl($ttl)
    {
        if ($this->ttl !== null && ($ttl > $this->ttl || $ttl == 0)) {
            return $this->ttl;
        }

        return $ttl;
    }
}