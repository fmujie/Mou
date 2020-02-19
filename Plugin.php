<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * 鼠标点击效果与图标追随(默认不启用)
 *
 * @package Mou
 * @author fmujie
 * @version 1.0.0
 * @link https://blog.fmujie.cn
 */
class Mou_Plugin implements Typecho_Plugin_Interface
{
    const STATIC_DIR = '/usr/plugins/Mou/static';
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->header = array(__CLASS__, 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array(__CLASS__, 'footer');
        Typecho_Plugin::factory('admin/menu.php')->navBar = array('Mou_Plugin', 'render');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $dir = self::STATIC_DIR;
        $staticDir = Helper::options()->pluginUrl . '/Mou/static/';
        $mouseFollowImageDir = $staticDir . 'images';

        $name = new Typecho_Widget_Helper_Form_Element_Text('word', null, 'Mou', _t('说点什么'));
        $form->addInput($name);
        
        //mouseFollow开关
        $options = [
            'default' => _t('关闭'),
            'paper' => "<img src='{$mouseFollowImageDir}/paper.ico' alt='paper'>",
        ];
        $followType = new Typecho_Widget_Helper_Form_Element_Radio('followType', $options, 'default', _t('follow样式，默认关闭'));
        $form->addInput($followType);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {}

    /**
     * 插件实现方法
     *
     * @access public
     * @return void
     */
    public static function render()
    {
        echo '<span class="message success">'
        . htmlspecialchars(Typecho_Widget::widget('Widget_Options')->plugin('Mou')->word)
            . '</span>';
        $heads = 1;
        $StaticCssUrl = Helper::options()->pluginUrl . '/Mou/static/css/';
        $StaticJsUrl = Helper::options()->pluginUrl . '/Mou/static/js/';
        if ($heads == 1) {
            echo '<link rel="stylesheet" href=" ' . $StaticCssUrl . 'style.css"/>';
            echo '<script type="text/javascript" src="https://cdn.staticfile.org/jquery/1.10.2/jquery.min.js"></script>';
            echo '<script type="text/javascript" src="' . $StaticJsUrl . 'main.js"></script>';
        }
    }

    /**
     *为header添加css文件
     * @return void
     */
    public static function header()
    {
        $StaticCssUrl = Helper::options()->pluginUrl . '/Mou/static/css/';
        echo '<link rel="stylesheet" href=" ' . $StaticCssUrl . 'style.css"/>';
    }

    /**
     *为footer添加js文件
     * @return void
     */
    public static function footer()
    {
        $followType = Typecho_Widget::widget('Widget_Options')->plugin('Mou')->followType;
        $dir = self::STATIC_DIR;
        $js = '';
        $StaticJsUrl = Helper::options()->pluginUrl . '/Mou/static/js/';
        echo '<script type="text/javascript" src="https://cdn.staticfile.org/jquery/1.10.2/jquery.min.js"></script>';
        $mouseFollowImageDir = $dir . '/images';
        // $test = 'test';
        // echo '<script type="text/javascript" src="' . $followType . $test . 'main.js"></script>';
        self::handleFollowType($followType);
        echo '<script type="text/javascript" src="' . $StaticJsUrl . 'main.js"></script>';
    }

    /*mouseFollowStyle*/
    private static function handleFollowType($followType)
    {
        if ($followType != 'default') {
            $followTypeImage = $followType . '.ico';
            $dir = self::STATIC_DIR;
            $mouseFollowImagesDir = self::STATIC_DIR . '/images';
            $js .= '<script>';
            $js .= <<<JS
            $(document).ready(function () {
                $('body').append('<div id="followMouseContent"><img src="{$mouseFollowImagesDir}/{$followTypeImage}" alt="followMouse"></div>');
                $(document).mousemove(function (e) {
                var x = e.pageX;
                var y = e.pageY;
                var mms = document.getElementById('followMouseContent');
                var cx = parseInt(x) - parseInt(80);
                var cy = parseInt(y) + parseInt(10);
                mms.style.left = cx + "px";
                mms.style.top = cy + "px";
                });
            });
JS;
            $js .= '</script>';
            echo $js;
        } else {
            echo '';
        }
    }
}
