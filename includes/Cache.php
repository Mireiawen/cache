<?php
declare(strict_types = 1);

namespace Mireiawen\Cache;

// Load the cache backend handler
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheLogicException;
use Phpfastcache\Exceptions\PhpfastcacheSimpleCacheException;
use Phpfastcache\Helper\Psr16Adapter;

/**
 * A helper wrapper for the PhpFastCache caching backend
 *
 * @package Mireiawen\Cache
 */
class Cache
{
	/**
	 * The actual cache backend
	 */
	protected $backend;
	
	/**
	 * Set up the caching backend driver
	 *
	 * @param string $driver
	 *    The cache backend driver to use
	 *
	 * @param array $config
	 *    The backend specific configurations
	 *
	 * @throws PhpfastcacheDriverCheckException
	 *    On Driver errors
	 *
	 * @throws PhpfastcacheLogicException
	 *    On logic errors
	 *
	 * @throws \Exception
	 *    In case of configuration errors
	 */
	public function __construct(string $driver, array $config = [])
	{
		// Do configuration based on the driver
		switch ($driver)
		{
		case 'files':
			$path = $this->ReadConfigVariable($config, 'path', NULL, TRUE);
			$this->backend = new Psr16Adapter($driver, ['path' => $path]);
			break;
		
		case 'memcache':
		case 'memcached':
			$host = $this->ReadConfigVariable($config, 'host', 'localhost');
			$port = $this->ReadConfigVariable($config, 'port', 11211);
			$this->backend = new Psr16Adapter(
				$driver,
				[
					'host' => $host,
					'port' => $port,
				]
			);
			break;
		
		case 'redis':
		case 'predis':
			$host = $this->ReadConfigVariable($config, 'host', 'localhost');
			$port = $this->ReadConfigVariable($config, 'port', 6379);
			$password = $this->ReadConfigVariable($config, 'password');
			$database = $this->ReadConfigVariable($config, 'database');
			
			$this->backend = new Psr16Adapter(
				$driver,
				[
					'host' => $host,
					'port' => $port,
					'password' => $password,
					'database' => $database,
				]
			);
			break;
		
		default:
			throw new \Exception(\sprintf(\_('Unknown cache driver %s'), $driver));
		}
	}
	
	/**
	 * Check if the key exists in the cache backend
	 *
	 * @param string $id
	 *    The cache ID to check for
	 *
	 * @return  bool
	 *    TRUE if $id is cached, FALSE otherwise
	 *
	 * @throws PhpfastcacheSimpleCacheException
	 *    On cache errors
	 */
	public function Available(string $id) : bool
	{
		$id = $this->SanitizeID($id);
		return $this->backend->has($id);
	}
	
	/**
	 * Invalidate or delete the key from the cache backend
	 *
	 * @param string $id
	 *    The cache ID to invalidate
	 *
	 * @throws PhpfastcacheSimpleCacheException
	 *    On cache errors
	 */
	public function Invalidate(string $id) : void
	{
		$id = $this->SanitizeID($id);
		$this->backend->delete($id);
	}
	
	/**
	 * Store a value to the cache backend
	 *
	 * @param string $id
	 *    The cache ID to store to
	 *
	 * @param mixed $value
	 *    The value to store to the cache
	 *
	 * @param int $ttl
	 *    The time to live for the cached item
	 *
	 * @throws PhpfastcacheSimpleCacheException
	 *    On cache errors
	 */
	public function Write(string $id, $value, int $ttl) : void
	{
		$id = $this->SanitizeID($id);
		$this->backend->set($id, $value, $ttl);
	}
	
	/**
	 * Retrieve a stored value from the cache backend
	 *
	 * @param string $id
	 *    The cache ID to read from
	 *
	 * @return mixed
	 *    The stored value of the key
	 *
	 * @throws \Exception
	 *    If the key is not stored in the cache backend
	 */
	public function Read(string $id)
	{
		$id = $this->SanitizeID($id);
		if ($this->Available($id))
		{
			return $this->backend->get($id);
		}
		
		throw new \Exception(\sprintf(\_('The key "%s" is not cached'), $id));
	}
	
	/**
	 * Read a variable from the configuration array
	 *
	 * @param array $config
	 *    The configuration array
	 *
	 * @param string $key
	 *    The key to look for
	 *
	 * @param mixed $default
	 *    The default value
	 *
	 * @param bool $required
	 *    If the value has to be available
	 *
	 * @return mixed
	 *    The key value, or default
	 *
	 * @throws \Exception
	 *    If the value is not available in the array and default is not set
	 */
	protected function ReadConfigVariable(array $config, string $key, $default = NULL, bool $required = FALSE)
	{
		if (!isset($config[$key]))
		{
			if ($required)
			{
				throw new \Exception(\sprintf(\_('Configuration is missing the required key "%s"'), $key));
			}
			
			return $default;
		}
		
		return $config[$key];
	}
	
	/**
	 * Sanitize the cache ID
	 *
	 * @param string $id
	 *    The cache ID to sanitize
	 *
	 * @return string
	 *    The sanitized ID
	 */
	protected function SanitizeID(string $id) : string
	{
		return str_replace
		(
			['/'],
			['___'],
			$id
		);
	}
}
