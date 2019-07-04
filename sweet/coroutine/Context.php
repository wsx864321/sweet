<?php
/**
 * Author wushixing.
 * Date: 2019/6/22
 * Time: 19:10
 */

namespace sweet\coroutine;

use Swoole\Http\Response;
use Swoole\Http\Request;

class Context{
    /**
     * @var Request
     *请求
     */
    private $request;
    /**
     * @var Response
     *响应
     */
    private $response;
    /**
     * @var array
     * map
     */
    private $map = [];
    /**
     * Context constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response){
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * @return Request
     */
    public function getRequest(){
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(){
        return $this->response;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value){
        $this->map[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key){
        return $this->map[$key];
    }
}