<?php
/**
 * Author wushixing.
 * Date: 2019/7/4
 * Time: 17:25
 */

namespace sweet\redis;

use sweet\core\Log;
use Swoole\Coroutine\Redis as SwRedis;

class Redis{
    /**
     * @var SwRedis
     */
    private $redis;
    /**
     * @var
     */
    private $config;
    /**
     * @var int 最后检查时间
     */
    private $lastPingTime;
    /**
     * @param $config
     * @return bool|mixed
     */
    public function connect($config){
        $this->config = $config;
        $this->redis = new SwRedis($config['options']);
        $ret = $this->redis->connect($config['host'], $config['port']);
        if(!$ret){
            Log::error("redis connect error");
        }

        return $ret;
    }

    /**
     * @desc 检查redis链接状态
     */
    private function checkConnect(){
        if($this->lastPingTime + $this->config['heartbeat'] <= time()) {
            try {
                if ($this->redis->ping() != "+PONG") {
                    $this->reconnect();
                }
            } catch (\Exception $e) {
                $this->reconnect();
            }
            $this->lastPingTime = time();
        }
    }

    /**
     * @return bool
     * @desc 重连机制
     */
    private function reconnect(){
        $this->redis = new SwRedis($this->config['options']);
        $ret = $this->redis->connect($this->config['host'],$this->config['port']);
        if(!$ret){
            Log::error("redis reconnect error");
        }

        return false;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @desc 利用call对redis进行操作
     */
    public function __call($name, $arguments){
        $this->checkConnect();
        return call_user_func_array([$this->redis,$name], $arguments);
    }
}