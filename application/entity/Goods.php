<?php
/**
 * Author wushixing.
 * Date: 2019/7/4
 * Time: 21:16
 */

namespace application\entity;

use sweet\mvc\Entity;

class Goods extends Entity{
    const TABLE_NAME = 'goods';
    const PK_ID = 'id';

    //以下对应的数据库字段名
    public $id;
    public $name;
    public $password;
}