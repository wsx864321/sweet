<?php
/**
 * Author wushixing.
 * Date: 2019/6/20
 * Time: 19:17
 */

$config = [
    'host'      => '127.0.0.1',
    'port'      => 9999,
    'mysql'     => [
        'pool_size'        => 10,     //连接池大小
        'enable_slave'     => false, //是否启用从库
        'pool_get_timeout' => 0.5, //当在此时间内未获得到一个连接，会立即返回。（表示所以的连接都已在使用中）
        'master'           => [
            'host'        => '127.0.0.1',   //数据库ip
            'port'        => 3306,          //数据库端口
            'user'        => 'root',        //数据库用户名
            'password'    => 'root', //数据库密码
            'database'    => 'test',   //默认数据库名
            'timeout'     => 0.5,       //数据库连接超时时间
            'charset'     => 'utf8mb4', //默认字符集
            'strict_type' => true,  //ture，会自动表数字转为int类型
        ],
        'slave'            => [
            [
                'host'        => 'xxxxxx',   //数据库ip
                'port'        => 3306,          //数据库端口
                'user'        => 'xxxx',        //数据库用户名
                'password'    => 'xxxxx', //数据库密码
                'database'    => 'test',   //默认数据库名
                'timeout'     => 0.5,       //数据库连接超时时间
                'charset'     => 'utf8mb4', //默认字符集
                'strict_type' => true,  //ture，会自动表数字转为int类型
            ],
        ],
    ],
    'router'    => [

    ],
    'templates' => [
        'cache' => APP_PATH . 'application' . DS . 'template' . DS . 'cache',
        'path'  => APP_PATH . 'application' . DS . 'template' . DS . 'default',
    ],
    'redis'     => [
        'pool_size'        => 10,     //连接池大小
        'pool_get_timeout' => 0.5, //当在此时间内未获得到一个连接，会立即返回。（表示所以的连接都已在使用中
        'host'             => '127.0.0.1',
        'port'             => 6379,
        'heartbeat'        => 5,
        'options'          => [
            'connect_timeout' => 1,//连接超时
            'timeout'         => 0.05,//读写超时
        ]
    ]
];

return $config;
