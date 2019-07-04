<?php
/**
 * Author wushixing.
 * Date: 2019/6/23
 * Time: 12:46
 */

namespace sweet\pool;

use sweet\db\Mysql;
use Swoole\Coroutine\Channel;

class MysqlPool{
    private static $_instance = null;
    private $pool;
    private $config;

    /**
     * MysqlPool constructor.
     * @param $config
     * @throws \Exception
     * @throws \sweet\db\Exception
     */
    public function __construct($config){
        if(empty($this->pool)){
            $this->pool = new Channel($config['pool_size']);
            $this->config = $config;

            for($i = 0;$i < $config['pool_size'];$i++){
                $conn = new Mysql();
                $ret = $conn->connect($config);

                if(!$ret){
                    throw new \Exception("mysql connect error");
                }else{
                    $this->push($conn);
                }
            }
        }
    }

    /**
     * @param null $config
     * @return null|MysqlPool
     * @throws \Exception
     * @desc 单例模式，获取连接池实例
     */
    public static function getInstance($config = null){
        if(empty(self::$_instance)){
            if(empty($config)) {
                throw new \Exception("mysql config empty");
            }
            self::$_instance = new self($config);
        }

        return self::$_instance;
    }

    /**
     * @param $data
     * @desc 将mysql connect压入pool
     */
    public function push($data){
        $this->pool->push($data);
    }

    /**
     * @return mixed
     * @throws \Exception
     * @desc 获取mysql连接
     */
    public function getMysqlConn(){
        $coon = $this->pool->pop($this->config['pool_get_timeout']);
        if(!$coon){
            throw new \Exception("get mysql connect failed");
        }

        return $coon;
    }

    /**
     * @return int|mixed
     * @desc 获取当前连接池可用对象长度
     */
    public function getLength(){
        return $this->pool->length();
    }
}