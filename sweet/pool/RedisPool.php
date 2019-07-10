<?php
/**
 * Author wushixing.
 * Date: 2019/7/4
 * Time: 17:15
 */

namespace sweet\pool;

use Co\Channel;
use sweet\redis\Redis as sweetRedis;

class RedisPool implements PoolInterface {
    private static $_instance = null;
    private $pool;
    private $config;

    public function __construct($config = null){
        $this->config = $config;
        $this->pool = new Channel($config['pool_size']);
        for($i = 0; $i < $config['pool_size'];$i++){
            $redis = new sweetRedis();
            $ret  = $redis->connect($config);

            if(!$ret){
                throw new \Exception("redis connect error");
            }else{
                $this->push($redis);
            }
        }
    }

    /**
     * @param $config
     * @return null|static
     * @desc 单例模式
     */
    public static function getInsatance($config = null){
        if(empty(self::$_instance)){
            self::$_instance = new static($config);
        }

        return self::$_instance;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function get(){
        $coon = $this->pool->pop($this->config['pool_get_timeout']);
        if(!$coon){
            throw new \Exception("get redis connect failed");
        }

        return $coon;
    }

    /**
     * @param $data\
     */
    public function push($data){
        $this->pool->push($data);
    }

    /**
     * @return mixed
     */
    public function getLength(){
        return $this->pool->length();
    }
}