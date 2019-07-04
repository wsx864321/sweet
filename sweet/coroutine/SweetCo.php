<?php
/**
 * Author wushixing.
 * Date: 2019/6/21
 * Time: 17:30
 */

namespace sweet\coroutine;

use Swoole\Coroutine;

class SweetCo{
    /**
     * @var array
     * 维护一个协程关系树，结构
     * ['当前协程id' => '根协程id']
     */
    public static $idMpas = [];

    /**
     * @return int
     * @desc 获取当前协程id
     */
    public static function getId(){
        return Coroutine::getCid();
    }

    /**
     * @param null $id
     * @return int|mixed|null
     * @desc 获取当前协程根协程id
     */
    public static function getRootId($id = null){
        if(is_null($id)){
            $id = self::getId();
        }

        if(isset(self::$idMpas[$id])){
            return self::$idMpas[$id];
        }

        return $id;
    }

    /**
     * @desc 设置request请求的第一个协程，其根id为自己
     */
    public static function setBaseId(){
        $id = self::getId();
        self::$idMpas[$id] = $id;
        return $id;
    }

    /**
     * @return bool
     * @desc 判断是否是根节点
     */
    public static function checkBaseCo(){
        $id = self::getId();

        if($id !== self::$idMpas[$id]){
            return false;
        }

        return true;
    }

    /**
     * @param $cb
     * @param null $deferCb
     * @return mixed
     */
    public static function create($cb, $deferCb = null){
        $pid = self::getId();

        return go(function () use ($cb, $pid, $deferCb){
            $id = Coroutine::getCid();
            defer(function() use ($deferCb, $id){
                var_dump($id);
                //清除idmaps当中的关系树的协程id
                self::clear($id);
                self::call($deferCb);
            });

            //设置idmaps
            $rootId = self::getRootId($pid);
            self::$idMpas[$id] = $rootId;
            self::call($cb);
        });
    }

    /**
     * @param $cb
     * @param array $args
     * @return null
     * @desc 执行回调函数
     */
    public static function call($cb, $args = []){
        if(empty($cb)){
            return null;
        }

        if(is_object($cb) || (is_string($cb) && file_exists($cb))){
            $ret = $cb(...$args);
        }elseif(is_array($cb)){
            list($obj, $method) = $cb;
            $ret = is_object($obj) ? $obj->$method(...$args) : $obj::$method(...$args);
        }

        return $ret;
    }

    /**
     * @desc 协程退出时清楚关系数
     */
    public static function clear($id = null){
        if(is_null($id)){
            $id = self::getId();
        }

        unset(self::$idMpas[$id]);
    }
}