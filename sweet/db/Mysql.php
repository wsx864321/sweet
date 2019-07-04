<?php
/**
 * Author wushixing.
 * Date: 2019/6/23
 * Time: 11:51
 */

namespace sweet\db;

use sweet\core\Config;
use sweet\core\Log;
use Swoole\Coroutine\MySQL as swMySQL;

class Mysql{
    private $master;
    private $slave = [];
    private $config;

    const MASET_SERVER = 1;
    const SLAVE_SERVER = 2;
    /**
     * @param $config
     * @return bool|mixed
     * @throws Exception
     * @desc 连接数据库
     */
    public function connect($config){
        $master = new swMySQL();
        $ret = $master->connect($config['master']);
        $this->config = $config;

        if($ret){
            $this->master = $master;
        }else{
            //todo log master connect failed
        }

        foreach ($config['slave'] as $item){
            $slave = new swMySQL();
            $ret = $slave->connect($item);

            if($ret){
                $this->slave[] = $slave;
            }else{
                //todo log
            }

        }

        return $ret;
    }

    /**
     * @param $type
     * @param int $index
     * @return bool|mixed
     * @throws Exception
     * @desc 重连机制
     */
    public function reconnect($type, $index = 0){
        if($type == self::MASET_SERVER){
            $mysql = new swMySQL();
            $ret = $mysql->connect($this->config['master']);

            if($ret){
                $this->master = $mysql;
            }else{
                //todo log

                return false;
            }

            return $mysql;
        }

        $mysql = new swMySQL();
        $ret = $mysql->connect($this->config[$index]);

        if($ret){
            $this->slave[$index] = $mysql;
        }else{
            //todo log
            return false;
        }

        return $mysql;
    }

    /**
     * @param $sql
     * @return array
     * @desc 选择主库还是从库
     */
    public function chooseDB($sql){
        //判断是否有slave
        if(Config::get('mysql.enable_slave')){
            if(strtolower(substr($sql,0,6)) == 'select'){
                $count = count($this->config['slave']);
                $randIndex = rand(0,$count - 1);
                $mysql = $this->slave[$randIndex];

                return [
                    'type'  => self::SLAVE_SERVER,
                    'index' => $randIndex,
                    'db'    => $mysql,
                ];
            }
        }

        return [
            'type'  => self::MASET_SERVER,
            'index' => 0,
            'db'    => $this->master,
        ];
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     * @throws \Exception
     * @desc 利用__call，实现mysql操作，并且在这个地方进行相关的状态检查，断线重连等机制
     */
    public function __call($name, $arguments){
        //todo 这样做过于简单粗暴，这个地方还有begin这样的事务处理，并不是只有query
        $sql = $arguments[0];
        //todo  改造成预处理 防止sql注入
        $dbRet = $this->chooseDB($sql);
        $db = $dbRet['db'];
        $ret = $db->$name($sql);
        //todo 这个地方可以和请求连起来，形成完整的链路追踪
        Log::info($sql);
        //这个地方不能!false简单的判断，因为查询的时候有可能是空
        if($ret === false){
            Log::warning('mysql query:{sql} false', ['{sql}' => $sql]);
            //判断是否是断线，并进行重新连接处理
            //todo 这个地方还要进行次数限制
            if(!$db->connected){
                $db = $this->reconnect($dbRet['type'], $dbRet['index']);
                //todo 进行db的判断，重连是否生效
                $ret = $db->$name($sql);
                //todo 这个地方可以和请求连起来，形成完整的链路追踪
                Log::info($sql);
            }

            if(!empty($this->mysql->errno)){
                throw new \Exception("errno:".$this->mysql->errno." error:".$this->mysql->error);
            }
        }

        return $this->parseResult($ret, $db);
    }

    /**
     * @param $ret
     * @param $db
     * @return array
     * @desc 格式化返回结果：查询：返回结果集，插入：返回新增id, 更新删除等操作：返回影响行数
     */
    public function parseResult($ret, $db){
        if($ret === true){
            return [
                'affectedRows' => $db->affected_rows,
                'insertId' => $db->insert_id,
            ];
        }

        return $ret;
    }
}