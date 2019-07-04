<?php
/**
 * Author wushixing.
 * Date: 2019/6/24
 * Time: 15:50
 */

namespace application\dao;

use sweet\mvc\Dao;
use application\entity\User as UserEntity;
use sweet\core\Singleton;

class User extends Dao{

    use Singleton;

    public function __construct(){
        parent::__construct(UserEntity::class);
    }

    public function getInfoById($id){
        return $this->fetch("id = ".$id);
    }

    public function add($data){
        return $this->insert($data);
    }
}