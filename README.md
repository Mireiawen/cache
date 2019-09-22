# Cache

A wrapper for [PhpFastCache](https://www.phpfastcache.com/) caching backend, using its [PSR-16](https://www.php-fig.org/psr/psr-16/) implementation. This wrapper supports some of the more common drivers, as well as has the methods to read, write, delete and check for keys in the backend.

* Class: `Cache`
* Namespace: `Mireiawen\Cache`

## Requirements
* Cache backend
* PHP7

## Installation
You can clone or download the code from the [GitHub repository](https://github.com/Mireiawen/cache) or you can use composer: `composer require mireiawen/cache`

## Methods

### __construct
    Cache::__construct(string $driver, array $config = [])

Set up the caching backend driver

#### Arguments
* **string** `$driver` - The cache backend driver to use, currently supported are:
  * files
  * memcache
  * memcached
  * redis
  * predis
* **array** `$config` - The backend specific configuration options
  * `files`:
    - `path` - The filesystem path where to store the cached data
  * `memcache`, `memcached`:
    - `host` - The hostname to connect to, defaults to `localhost`
    - `port` - The port to connect to, defaults to `11211`
  * `redis`, `predis`:
    - `host` - The hostname to connect to, defaults to `localhost`
    - `port` - The port to connect to, defaults to `6379`
    - `password` - The password to connect with, defaults to `NULL`
    - `database` - The database to use, defaults to `NULL`

#### Exceptions thrown
##### PhpfastcacheDriverCheckException
* On Driver errors

##### PhpfastcacheLogicException
* On PhpFastCache logic errors

##### \Exception
* In case of configuration errors


### Available
    Cache::Available(string $id)

Check if the key exists in the cache backend

#### Arguments
* **string** `$id` - The cache ID to check for

#### Return value
* **bool** - TRUE if $id is cached, FALSE otherwise
 
#### Exceptions thrown
##### PhpfastcacheSimpleCacheException
* On cache errors

### Invalidate
    Cache::Invalidate(string $id)

Invalidate or delete the key from the cache backend
#### Arguments
* **string** `$id` - The cache ID to invalidate

#### Exceptions thrown
##### PhpfastcacheSimpleCacheException
* On cache errors
	
### Write
    Cache::Write(string $id, mixed $value, int $ttl)

Store a value to the cache backend


#### Arguments
* **string** `$id` - The cache ID to store to
* **mixed** `$value` - The value to store to the cache
* **int** `$ttl` - The time to live for the cached item

#### Exceptions thrown
##### PhpfastcacheSimpleCacheException
* On cache errors
	
### Read
    Cache::Read(string $id)

Retrieve a stored value from the cache backend

#### Arguments
* **string** `$id` - The cache ID to read from

#### Return value
* **mixed** - The stored value of the key
 
#### Exceptions thrown
##### PhpfastcacheSimpleCacheException
* On cache errors
