<?php
/**
 * Author wushixing.
 * Date: 2019/6/24
 * Time: 21:11
 */

namespace sweet\mvc;

use sweet\helper\Template;
use sweet\pool\Context;

class BaseController{
    /**
     * @var 请求
     */
    protected $request;
    /**
     * @var 响应
     */
    protected $response;
    /**
     * @var 模板
     */
    protected $template;

    public function __construct(){
        $context = Context::getContext();
        $this->request  = $context->getRequest();
        $this->response = $context->getResponse();
        $this->template = Template::getInstance()->twig;
    }

}