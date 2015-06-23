<?php
/**
 * @copyright ©2014 Quicken Loans Inc. All rights reserved. Trade Secret,
 *    Confidential and Proprietary. Any dissemination outside of Quicken Loans
 *    is strictly prohibited.
 */

namespace MCP\Cache;

/**
 * Provide convenience methods and wrappers for caching in repositories.
 *
 * This trait gracefully handles cases where no cache is set.
 *
 * NEVER access properties set in traits directly from within the consumer of the trait!
 *
 * For example:
 * Use $this->cache()
 * Not $this->cache
 */
trait CachingTrait
{
    /**
     * @var CacheInterface|null
     */
    private $cache;

    /**
     * @var int|null
     */
    private $cacheTTL;

    /**
     * @return CacheInterface|null
     */
    private function cache()
    {
        return $this->cache;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    private function getFromCache($key)
    {
        if (!$this->cache()) {
            return null;
        }

        return $this->cache()->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param mixed $ttl
     * @return null
     */
    private function setToCache($key, $value, $ttl = 0)
    {
        if (!$this->cache()) {
            return;
        }

        $params = func_get_args();

        // Use default TTL if none is provided
        if ($this->cacheTTL && func_num_args() < 3) {
            array_push($params, $this->cacheTTL);
        }

        return call_user_func_array([$this->cache(), 'set'], $params);
    }

    /**
     * @param CacheInterface $cache
     * @return null
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param int $ttl
     * @return null
     */
    public function setCacheTTL($ttl)
    {
        $this->cacheTTL = (int) $ttl;
    }
}