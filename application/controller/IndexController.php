<?php
/**
 * Author wushixing.
 * Date: 2019/6/20
 * Time: 19:33
 */


namespace application\controller;

use application\dao\Goods;
use application\dao\User;
use sweet\coroutine\SweetCo;
use sweet\pool\Context;
use sweet\mvc\BaseController;
use sweet\redis\RedisDriver;

class IndexController extends BaseController{

    public static $test = 1;

    public function actionIndex(){
       // return "hello world";
//        $redis = RedisDriver::factory();
//        var_dump($redis);
//        $ret = $redis->get("name");
//        return $ret;
//        $context = Context::getContext();
//        $request = $context->getRequest();
//        return 'i am family by route!' . json_encode($request->get);
}

    public function actionTest(){
        $model = User::getRequestInstance();
//
//        SweetCo::create(function(){
//            User::getRequestInstance();
//            \Co::sleep(1);
//        });
//
//        SweetCo::create(function(){
//            $model = User::getRequestInstance();
//            $ret = $model->getInfoById(1);
//            var_dump($model);
//        });

        $ret = $model->getInfoById(1);


        $goodsModel = Goods::getRequestInstance();
        $goodsModel->getInfoById(1);
        return json_encode($ret);
    }

    public function actionAdd(){
        $data['name']     = htmlspecialchars($this->request->get["name"]);
        $data['password'] = htmlspecialchars($this->request->get['passwd']);

        $model = new User();
        $model->add($data);
    }

    public function actionView(){
        return $this->template->render('index/index.twig', [
            'name' => 'tong'
        ]);
    }
}
