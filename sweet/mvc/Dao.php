<?php
/**
 * Author wushixing.
 * Date: 2019/6/23
 * Time: 14:59
 */

namespace sweet\mvc;

use sweet\coroutine\SweetCo;
use sweet\pool\MysqlPool;

/**
 * Class Dao
 * @package sweet\mvc
 * todo  存在sql注入风险
 */
class Dao{
    /**
     * @var 数据表表名
     */
    private $tableName ;
    /**
     * @var array
     */
    private $dbs = [];
    /**
     * @var
     */
    private $pkId;
    /**
     * @var
     */
    private $entity;
    /**
     * @var 数据库配置名，处理多个数据库
     */
    private $dbTag;

    /**
     * Dao constructor.
     * @param $entity
     * @throws \Exception
     */
    public function __construct($entity){
        $this->entity = $entity;
        $this->entity = new \ReflectionClass($entity);
        $this->tableName = $this->entity->getConstant('TABLE_NAME');
        $this->pkId = $this->entity->getConstant('PK_ID');
    }

    /**
     * @return mixed
     * @throws \Exception
     * @desc 每次在执行数据库选择数据库连接资源
     * todo 需要优化的是这里面需要进行多个DB的选择
     */
    public function getDb(){
        $id = SweetCo::getId();
        var_dump(__CLASS__."---".json_encode($this->dbs));
        if(empty($this->dbs[$id])){
            //不同协程不能复用mysql连接，所以通过协程id进行资源隔离
            //达到同一协程只用一个mysql连接，不同协程用不同的mysql连接
            $this->dbs[$id] = MysqlPool::getInstance()->getMysqlConn();
            defer(function(){
                $this->recycle();
            });
        }

        return $this->dbs[$id];
    }

    /**
     * @throws \Exception
     * @desc 释放资源
     */
    public function recycle(){
        $id = SweetCo::getId();
        if(!empty($this->dbs[$id])){
            $mysql = $this->dbs[$id];
            MysqlPool::getInstance()->push($mysql);
            unset($this->dbs[$id]);
        }
    }

    /**
     * @return 数据表表名
     */
    public function getTableName(){
        return $this->tableName;
    }


    public function fetchById(){

    }

    /**
     * @param $where
     * @param string $fields
     * @param null $orderBy
     * @param int $limit
     * @return mixed
     * @throws \Exception
     * @desc 查询语句
     */
    public function fetch($where, $fields = "*", $orderBy = null, $limit = 0){
        $sql = "select ".$fields." from ".$this->tableName." where ".$where;

        if(!empty($orderBy)){
            $sql = $sql." order by ".$orderBy;
        }

        if($limit){
            $sql = $sql." limit ".$limit;
        }

        $ret = $this->getDb()->query($sql);
        return $ret;
    }

    /**
     * @param array $array
     * @return bool
     * @throws \Exception
     * @desc 插入数据
     */
    public function insert(array $array){

        $sql = 'insert into '.$this->tableName.'(';
        $sql .= ltrim(implode(',' ,array_keys($array)), ',').") values(";
        $sql .= ltrim(implode(',', $array),',').")";

        $ret = $this->getDb()->query($sql);

        if (!empty($ret['insert_id'])) {
            return $ret['insert_id'];
        }

        return false;
    }

    /**
     * @param array $array
     * @param $where
     * @return mixed
     * @throws \Exception
     * @desc 更新数据
     */
    public function update(array $array, $where){
        if (empty($where)) {
            throw new \Exception('delete 必需有where条件限定');
        }

        if (empty($array)){
            throw new \Exception('delet必须有修改数据');
        }

        $sql = "update ".$this->tableName." set ";
        foreach($array as $key => $value){
            $sql .= $key." = ".$value.",";
        }
        $sql = rtrim($sql,',');
        $sql .= "where ".$where;

        $ret = $this->getDb()->query($sql);
        return $ret['affected_rows'];

    }

    /**
     * @param $where
     * @return mixed
     * @throws \Exception
     * @desc 删除数据
     */
    public function delete($where){
        if (empty($where)) {
            throw new \Exception('delete 必需有where条件限定');
        }

        $sql = "delete from ".$this->tableName." where ".$where;
        $ret = $this->getDb()->query($sql);
        return $ret;
    }
}