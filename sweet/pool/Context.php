<?php
/**
 * Author wushixing.
 * Date: 2019/6/22
 * Time: 14:26
 */

namespace sweet\pool;

use sweet\coroutine\SweetCo;

class Context{

    private static $pool = [];

    /**
     * @return mixed|null
     * 获取context
     */
    public static function getContext(){
        $id = SweetCo::getRootId();

        if(isset(self::$pool[$id])){
            return self::$pool[$id];
        }

        return null;
    }

    /**
     * @desc  清除context
     */
    public static function clear($id = null){
        if(is_null($id)){
            $id = SweetCo::getRootId();
        }

        if (isset(self::$pool[$id])) {
            unset(self::$pool[$id]);
        }
    }

    /**
     * @param $context
     * 设置context
     */
    public static function set($context){
        $id = SweetCo::getRootId();
        self::$pool[$id] = $context;
    }
}