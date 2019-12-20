<?php
/**
 * Author wushixing.
 * Date: 2019/6/20
 * Time: 18:42
 */

namespace sweet;

use sweet\pool\MysqlPool;
use sweet\pool\RedisPool;
use Swoole;
use sweet\core\Config;
use sweet\core\Router;
use sweet\core\Log;
use sweet\coroutine\SweetCo;
use Swoole\Http\Response;
use Swoole\Http\Request;

class Sweet{
    public static function run(){
        //自动加载
        spl_autoload_register(__CLASS__."::autoLoader");
        //加载配置
        Config::load();
        //创建http server
        $http = new Swoole\Http\Server("0.0.0.0",9999);

        $http->set([
            'worker_num' => 1,
        ]);
        //服务启动
        $http->on('start',function($serv){
            //日志初始化
            Log::init();
            file_put_contents(APP_PATH . DS . 'bin' . DS . 'master.pid', $serv->master_pid);
            file_put_contents(APP_PATH . DS . 'bin' . DS . 'manager.pid', $serv->manager_pid);
            Log::info("http server start! {host}: {port}, masterId:{masterId}, managerId: {managerId}", [
                '{host}' => Config::get('host'),
                '{port}' => Config::get('port'),
                '{masterId}' => $serv->master_pid,
                '{managerId}' => $serv->manager_pid,
            ]);
        });

        $http->on('workerStart',function($serv, $workerId){
            if (function_exists('opcache_reset')) {
                //清除opcache 缓存
                \opcache_reset();
            }

            try{
                //初始化mysql连接池
                $mysqlConfig = Config::get('mysql');
                MysqlPool::getInstance($mysqlConfig);
                //初始化redis连接池
                $redisConfig = Config::get('redis');
                RedisPool::getInsatance($redisConfig);
            }catch (\Throwable $e){
                print_r($e);
                $serv->shutdown();
            }
        });

        $http->on('request',function(Request $request, Response $response){
            //设置根协程id
            SweetCo::setBaseId();
            //初始化上下文
            $context = new \sweet\coroutine\Context($request, $response);
            //context存入pool
            \sweet\pool\Context::set($context);
            //设置defer,清楚上下文和协程关系树
            defer(function(){
                \sweet\pool\Context::clear();
                \sweet\coroutine\SweetCo::clear();
            });

            try{
                $ret = Router::dispatch($request->server['path_info']);
                $response->end($ret);
            }catch (\Exception $e){
                Log::alert($e->getMessage(), $e->getTrace());
                $response->end($e->getMessage());
            }catch (\Error $e){
                Log::emergency($e->getMessage(), $e->getTrace());
                $response->status(500);
            }catch (\Throwable $e){
                Log::emergency($e->getMessage(), $e->getTrace());
                $response->status(500);
            }

        });

        $http->start();
    }

    public static function autoLoader($class){
        $fileName = APP_PATH.'/'.str_replace('\\', '/', $class).'.php';

        if(!file_exists($fileName)){
            return;
        }

        require APP_PATH.'/'.str_replace('\\', '/', $class).'.php';

    }
}