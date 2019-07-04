<?php
/**
 * Author wushixing.
 * Date: 2019/7/4
 * Time: 20:09
 */

namespace sweet\redis;
use sweet\coroutine\SweetCo;
use sweet\pool\RedisPool;

/**
 * Class RedisDriver
 * @package sweet\redis
 * redis 驱动类
 */
class RedisDriver{
    /**
     * @var array
     */
    public static $connetions = [];

    /**
     * @return mixed|null|static
     */
    public static function factory(){
        //获取当前协程id，每个协程都只能用自己的redis连接
        $id = SweetCo::getId();
        var_dump(self::$connetions);
        if(isset(self::$connetions[$id])){
            return self::$connetions[$id];
        }

        try{
            $redis = RedisPool::getInsatance()->get();
        }catch (\Exception $e){
            throw new Exception('redis pool is empty');
        }

        defer(function() use ($redis, $id){
           self::recycle($redis, $id);
        });

        return $redis;
    }

    /**
     * @param null $id
     * @desc 释放连接
     */
    public static function recycle($redis, $id = null){
        if(empty($id)){
            $id = SweetCo::getId();
        }

        RedisPool::getInsatance()->push($redis);
        unset(self::$connetions[$id]);
    }
}