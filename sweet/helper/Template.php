<?php
/**
 * Author wushixing.
 * Date: 2019/6/27
 * Time: 13:19
 */

namespace sweet\helper;



use sweet\core\Config;
use sweet\core\Singleton;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class Template
 * @package sweet\helper
 * @desc  在大型前后端的分离的项目中，实际上是没有必要有模板的
 * 但是对于中小型项目来说，模板变量对于开发的便捷程度会有极大的提升
 */

class Template{

    use Singleton;

    public $twig;

    public function __construct(){
        $config = Config::get('templates');
        $loader = new FilesystemLoader($config['path']);
        $this->twig = new Environment($loader, array(
            'cache'       => $config['cache'],// 设置是否使用cache,false时禁用cache
            'auto_reload' => true,
        ));
    }

    /**
     * @desc 清除模板缓存
     */
    public function clear(){
        if($this->twig){
            $this->twig->clearTemplateCache();
        }
    }
}