<?php
/**
 * Author wushixing.
 * Date: 2019/7/4
 * Time: 21:18
 */

namespace application\dao;

use sweet\mvc\Dao;
use application\entity\Goods as GoodsEntity;
use sweet\core\Singleton;

class Goods extends Dao{

    use Singleton;

    public function __construct(){
        parent::__construct(GoodsEntity::class);
    }

    public function getInfoById($id){
        return $this->fetch("id = ".$id);
    }

//    public function add($data){
//        return $this->insert($data);
//    }
}