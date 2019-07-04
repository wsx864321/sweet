<?php
/**
 * Author wushixing.
 * Date: 2019/6/23
 * Time: 13:51
 */

namespace sweet\mvc;

class Entity{
    public function __construct(array $arr){
        if(empty($arr)){
            return $this;
        }

        foreach($arr as $key => $value){
            if(property_exists($this, $key)){
                $this->$key = $value;
            }
        }
    }
}