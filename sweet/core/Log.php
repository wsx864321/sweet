<?php
/**
 *
 * Author wushixing.
 * Date: 2019/6/21
 * Time: 16:30
 */

namespace sweet\core;

use SeasLog;

class Log{
    /**
     * @desc  设置log路径
     */
    public static function init(){
        SeasLog::setBasePath(APPLICATION_PATH . DS . 'log');
    }

    /**
     * @param $name
     * @param $arguments
     * @desc 设置proxy
     */
    public static function __callStatic($name, $arguments){
        forward_static_call_array(['SeasLog', $name], $arguments);
    }
}
