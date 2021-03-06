<?php
/**
 * Author wushixing.
 * Date: 2019/7/4
 * Time: 18:14
 */

namespace sweet\pool;
/**
 * Interface PoolInterface
 * @package sweet\pool
 * @desc 连接池接口
 */
interface PoolInterface{
    /**
     * @return mixed
     * @desc 获取连接
     */
    public function get();

    /**
     * @param $data
     * @return mixed
     * @desc 放入到连接池
     */
    public function push($data);

    /**
     * @return mixed
     * 获取长度
     */
    public function getLength();
}