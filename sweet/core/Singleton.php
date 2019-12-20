<?php
/**
 * Author wushixing.
 * Date: 2019/6/26
 * Time: 11:41
 */

namespace sweet\core;

use sweet\coroutine\SweetCo;

Trait Singleton{

    private static $instance;
    private static $coInstance = [];

    /**
     * @param array ...$args
     * @return static
     * @desc 全局单例
     */
    public static function getInstance(...$args){
        if(empty(self::$instance)){
            //注意以后这个地方最好别用new self,这个地方需要延迟静态绑定
            self::$instance = new static(...$args);
        }

        return self::$instance;
    }

    /**
     * @param array ...$args
     * @return mixed
     * @desc 协程内单例
     */
    public static function getCoInstance(...$args){
        $id = SweetCo::getId();
        if(empty(self::$coInstance[$id])){
            self::$coInstance[$id] = new static(...$args);
            defer(function () use ($id) {
                unset(self::$coInstance[$id]);
            });
        }

        return self::$coInstance[$id];
    }

    /**
     * @param array ...$args
     * @return mixed
     * @desc 请求级别的单例,事实上这个单例如果是在子协程中创建的话，其实还是只是协程级别的单例，甚至
     * 都不能说是协程级别的，因为协程的调度是不可控的，也有可能几个子协程中都能访问到
     * 这种请求级别的单例的坑也会很多，在协程并发调度的时候也很容易遇到问题，
     * 所以尽量不要在子协程中创建请求级别的单例
     */
    public static function getRequestInstance(...$args){
        $rootId = SweetCo::getRootId();
        if(empty(self::$coInstance[$rootId])){
            self::$coInstance[$rootId] = new static(...$args);
            defer(function () use ($rootId) {
                unset(self::$coInstance[$rootId]);
            });
        }

        return self::$coInstance[$rootId];
    }
}