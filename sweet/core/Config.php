<?php
/**
 * Author wushixing.
 * Date: 2019/6/20
 * Time: 19:12
 */

namespace sweet\core;

class Config{
    /**
     * @var array
     * 配置map
     */
    public static $configMap = [];

    /**
     * @desc 加载到配置
     */
    public static function load(){
        $configPath = APP_PATH."application/config/default.php";
        self::$configMap = require_once($configPath);
    }

    /**
     * @param $key
     * @return bool|mixed
     * 获取配置
     */
    public static function get($key){
        if(empty($key)){
            return false;
        };

        $arr = explode('.', $key);

        $config = self::$configMap;

        foreach($arr as $value){
            $config = $config[$value];
        }

        return $config;
    }
}