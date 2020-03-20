<?php
/**
 * Author wushixing.
 * Date: 2020/3/20
 * Time: 17:48
 */

namespace sweet\rateLimit;

use sweet\redis\RedisDriver;
use Swoole\Redis;

class RateLimit{
    private $_rate;//每秒钟放入到令牌桶的大小
    private $_redis;
    private $_leftToken;//剩下的令牌
    private $_lastTs;//最后一次获取令牌的时间
    private $_maxToken;//令牌桶最大值
    private $_lastTsKey;//最后获取令牌的时间的redis key
    private $_leftTokenKey;//剩余令牌的redis key

    const PREFIX_TOKEN_LAST_FETCH_TIME = "flow:limit:token:last:fetch:time:";//令牌最后活一次获取的时间的key
    const PREFIX_LEFT_TOKEN = "flow:limit:left:token:";//剩余的token

    //需要配置的接口api
    const API_DSPV2_JUDGEV2 = "dspv2:jugv2";

    //限流配置
    private $_limitConfig = [
        self::API_DSPV2_JUDGEV2 =>[
            'rate'     => 3,
            'maxToken' => 200,
        ],
    ];

    /**
     * RateLimit constructor.
     * @param $api
     */
    public function __construct($api){
        $this->_rate         = $this->_limitConfig[$api]['rate'];
        $this->_maxToken     = $this->_limitConfig[$api]['maxToken'];
        $this->_lastTsKey    = self::PREFIX_TOKEN_LAST_FETCH_TIME . self::API_DSPV2_JUDGEV2;
        $this->_leftTokenKey = self::PREFIX_LEFT_TOKEN . self::API_DSPV2_JUDGEV2;

        $this->_redis = RedisDriver::factory();
        $lastTs = $this->_redis->get($this->_lastTsKey);

        if(!$lastTs){
            $this->_lastTs = 0;//最后一次获取的时间不存在，默认为0
        }else{
            $this->_lastTs = $lastTs;
        }


        $leftToken = $this->_redis->get($this->_leftTokenKey);
        if($leftToken !== 0 && !$leftToken){
            $this->_leftToken = $this->_maxToken;
        }else{
            $this->_leftToken = $leftToken;
        }
    }

    /**
     * @return bool
     * @desc 获取令牌
     */
    public function get(){
        $this->_makeSpace();

        if($this->_leftToken <= 0){
            return false;
        }

        $this->_redis->set($this->_leftTokenKey, intval($this->_leftToken - 1), 86400);//对令牌桶进行减1操作
        return true;
    }

    /**
     * @desc 对令牌桶进行增加
     */
    private function _makeSpace(){
        $cuttentTime = time();
        $deltaTs = $cuttentTime - $this->_lastTs;
        $deltaToken = $this->_rate*$deltaTs;
        $this->_leftToken += $deltaToken;

        if($this->_leftToken > $this->_maxToken){
            $this->_leftToken = $this->_maxToken;
        }

        $this->_redis->set($this->_lastTsKey, intval($cuttentTime), 86400);
    }
}