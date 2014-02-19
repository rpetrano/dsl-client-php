<?php
namespace NGS\Cache\Apc;
use \Psr\Cache\PoolInterface;
use NGS\Cache\InvalidArgumentException;

class Pool implements PoolInterface
{
    protected $poolKey;

    public function __construct($poolKey)
    {
        $this->poolKey = $poolKey;
    }

    public function getItem($key)
    {
        return new Item($this->poolKey.'_'.$key);
    }

    public function getItems(array $keys)
    {
        $result = array();
        foreach ($keys as $key)
        {
            $item = $this->getItem($key);
            $result[$item->getKey()] = $key->get();
        }

        return $result;
    }

    public function clear()
    {
        $iterator = new \APCIterator('user');
        foreach ($iterator as $i)
        {
            $item = new Item($i['key']);
            if (strpos($i['key'], $this->poolKey) === 0)
                $item->delete();
        }
    }
}
