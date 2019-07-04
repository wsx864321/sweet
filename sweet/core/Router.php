<?php
/**
 * Author wushixing.
 * Date: 2019/6/20
 * Time: 19:14
 */

namespace sweet\core;

use sweet\pool\Context;
use FastRoute\Dispatcher;

class Router{

    const PARAMS_COUNT = 2;//controller 和  action
    const CONTROLLER   = 'Controller';
    const ACTION       = 'action';

    public static function dispatch($uri){
        //屏蔽favicon.ico图标请求
        if ($uri == '/favicon.ico') {
            return '';
        }

        $uri    = trim($uri, '/');
        $params = explode("/", $uri);
        //确定参数个数
        $count = count($params);
        $controllerStr = "";

        for($i = 0; $i < $count -1; $i++){
            $controllerStr .= "\\".$params[$i];
        }

        //命名空间要加上
        $controllerName = "application\\controller".$controllerStr.self::CONTROLLER;
        $dispatch = new $controllerName();
        $actionName = "action".end($params);
        if(APP_DUBUG){
            $context = Context::getContext();
            $request = $context->getRequest();
            Log::info("request_uri:".$request->server['request_uri']." ".$request->server['server_protocol']." ip:".$request->server['remote_addr']);
        }

        return call_user_func([$dispatch, $actionName]);
    }
}