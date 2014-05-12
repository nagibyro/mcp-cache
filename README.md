# MCP Cache

Caching standard for mcp services

### Usage

The `CacheInterface` has only two methods - `get` and `set`.

```php
public function get($key);
```

Get takes any string as a key and does a lookup of the data.

##### Please Note:
- Missing data will return null. There is no difference between a cache hit of a `null` value and a cache miss.
- It is not necessary to serialize objects before storage. Serialization will be optimized by each cache implementation.

```php
public function set($key, $value, $ttl = 0);
```

Set data in the cache. A boolean will be returned to indicate whether the data was saved.

##### Please Note:
- Resources cannot be cached.
- TTL is the time in seconds the data should live until expired. A time to live of `0` will never expire the data.

### MemoryCache

The `MemoryCache` is a very basic cache for caching data that only lives through the lifetime
of the request. This cache ignores `ttl`.

```php
use MCP\Cache\MemoryCache;

$cache = new MemoryCache

// Store data
$cache->set('key', $data);

// Store data with expiration of 10 minutes  - note that ttl is ignored for this cacher
$cache->set('key', $data, 600);
```

### SkeletorSessionCache

This cache will store data in the skeletor session.

An optional suffix may be provided to salt the cache keys. This can be used to invalidate the entire cache
between code pushes or other configuration changes.

```php
use MCP\Cache\SkeletorSessionCache;
use MCP\DataType\Time\Clock;

$clock = new Clock('now', 'UTC');
$suffix = '6038aa7'; // optional
$cache = new SkeletorSessionCache($session, $clock, $suffix);

// Store data
$cache->set('key', $data);

// Store data with expiration of 10 minutes
$cache->set('key', $data, 600);
```

### PredisCache

This cache will store data in the redis using [predis](https://github.com/nrk/predis).

An optional suffix may be provided to salt the cache keys. This can be used to invalidate the entire cache
between code pushes or other configuration changes.

Setting a key to `null` will delete the key rather than setting the value to `null`.
It is not possible to store a `null` value with the predis cacher.

```php
use Predis\Client;
use MCP\Cache\PredisCache;

$predis = new Client;
$suffix = '6038aa7'; // optional
$cache = new PredisCache($predis, $suffix);

// Store data
$cache->set('key', $data);

// Store data with expiration of 10 minutes
$cache->set('key', $data, 600);
```

### Building

A PSR-4 compatible autoloader is required to use this library. Composer is recommended.

### Installing

Install development dependencies

    bin/install

Wipe compiled files:

    bin/clean

Run tests:

    vendor/bin/phpunit
