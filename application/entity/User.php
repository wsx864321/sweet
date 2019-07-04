<?php
/**
 * Author wushixing.
 * Date: 2019/6/24
 * Time: 15:50
 */

namespace application\entity;

use sweet\mvc\Entity;

class User extends Entity{
    const TABLE_NAME = 'user';
    const PK_ID = 'id';

    //以下对应的数据库字段名
    public $id;
    public $name;
    public $count;
    public $sort;
    public $unk_key;
}