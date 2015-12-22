<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Shared\Collector\Code\Reader\KeyValue;

use Spryker\Shared\Library\Storage\Adapter\KeyValue\ReadInterface;
use Spryker\Shared\Library\Storage\Adapter\KeyValue\ReadWriteInterface;
use Spryker\Shared\Collector\Code\Reader\ReaderInterface;

class RedisReader implements ReaderInterface
{

    /**
     * @var ReadInterface
     */
    protected $redis;

    /**
     * Constructor
     *
     * @param ReadWriteInterface $kvAdapter
     */
    public function __construct(ReadWriteInterface $kvAdapter)
    {
        $this->redis = $kvAdapter;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function read($key)
    {
        return $this->redis->get($key);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'redis-reader';
    }

}