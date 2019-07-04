#!/bin/bash
#### sweet framework start
#### author:wushxing
#### date:2019/06/27

#php目录路径
PHP_BIN=`which php`
#sweet bin目录
APP_BIN=`pwd`
#server目录
SERVER_PATH=$APP_BIN/..
#application_file文件路径
APPLICATION_PATH=$SERVER_PATH/application
#获取主进程id

getMasterPid(){
    #if []左右两边都要有空格
    if [ -f "$APP_BIN/master.pid" ]
    then
        MASTER_ID=`cat $APP_BIN/master.pid`
        echo $MASTER_ID
    else
        echo -1
    fi
}
#获取管理进程id
getManagerPid(){
    #if []左右两边都要有空格
    if [ -f "$APP_BIN/manager.pid"]
    then
        MANAGER_ID=`cat $APP_PATH/manager.pid`
        echo $MANAGER_ID
    else
        echo -1
    fi
}
#获取命令行输入的参数
ARG=$1
case $ARG in
    start)
        #开启sweet
        PID=`getMasterPid`
        if [ $PID == -1 ]
        then
            #启动服务
            echo "Starting server"
            cd $APPLICATION_PATH
            $PHP_BIN "index.php"
            echo "done"
            exit
        else
            echo "server is runnig"
        fi
        ;;
    stop)
        #停止sweet
        PID=`getMasterPid`
        if [ $PID == -1 ]
        then
            echo "server is not running"
            exit 1
        else
            #停止服务
            echo "Gracefully shutting down server "
            kill $PID
           # sleep 1
            unlink $APP_BIN/master.pid
            unlink $APP_BIN/manager.pid
        fi

        ;;
    reload)
        #重启sweet
        ;;
esac
