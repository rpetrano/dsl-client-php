<?php
namespace NGS\Cache\Apc;
use Psr\Cache\ItemInterface;
use NGS\Cache\InvalidArgumentException;

class Item implements ItemInterface
{
	protected $key;

	public function __construct($key)
	{
		if (!is_string($key))
			throw new InvalidArgumentException("Not a valid key provided.");

		$this->key = $key;
	}

	public function getKey()
	{
		return $this->key;
	}

	public function get() {
		if ($this->isHit() === false)
			return null;

		$success = false;
		$value = apc_fetch($this->key, $success);
		if ($success == false)
			return null;

		return $value;
	}

	public function set($value = true, $ttl = 0)
	{
		$ttlSeconds = 0;
		$invalidTTL = false;

		if ($ttl instanceof \DateTime)
		{
			$now = new DateTime();
			$ttlSeconds = $ttl->getTimestamp() - $now->getTimestamp();
			$invalidTTL = ($ttlSeconds === 0);
		}
		else if (is_int($ttl))
		{
			$ttlSeconds = $ttl;
		}
		else
		{
			throw new InvalidArgumentException("TTL must be an instance of DateTime or int.");
		}

		if ($ttlSeconds < 0 || $invalidTTL)
			throw new InvalidArgumentException("TTL must be positive integer value or 0.");

		return apc_store($this->key, $value, $ttlSeconds);
	}

	public function isHit()
	{
		return $this->exists();
	}

	public function delete()
	{
		if ($this->exists())
			apc_delete($this->key);
	}

	public function exists()
	{
		return apc_exists($this->key);
	}
}
