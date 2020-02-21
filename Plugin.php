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

        //原先Hello World保留
        $name = new Typecho_Widget_Helper_Form_Element_Text('word', null, 'Mou', _t('说点什么'));
        $form->addInput($name);
        
        //mouseFollow开关
        $options = [
            'default' => _t('关闭'),
            'paper' => "<img src='{$mouseFollowImageDir}/paper.ico' alt='paper'>",
        ];
        $followType = new Typecho_Widget_Helper_Form_Element_Radio('followType', $options, 'default', _t('follow样式，默认关闭'));
        $form->addInput($followType);

        //imagesExpand开关
        $imgOptions = [
            // 'default' => _t('关闭'),
            '800' => _t('适中'),
            '1000' => _('较大'),
            '1200' => _t('最大'),
        ];
        $expandImgType = new Typecho_Widget_Helper_Form_Element_Radio('expandImgType', $imgOptions, '800', _t('图片双击放大效果，默认适中'));
        $form->addInput($expandImgType);

        //imagesExpandBg选择
        $imgBgOptions = [
            'rgba(0,0,123,0.4)' => _t('幻影紫'),
            'rgba(160,238,225,0.4)' => _('纯净绿'),
            'rgba(236,173,158,0.4)' => _t('暖心红'),
        ];
        $expandImgBgType = new Typecho_Widget_Helper_Form_Element_Radio('expandImgBgType', $imgBgOptions, 'rgba(0,0,123,0.4)', _t('图片双击放大后的背景颜色，默认幻影紫'));
        $form->addInput($expandImgBgType);
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
        $heads = 1;//暂定开关
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
        $dir = self::STATIC_DIR;
        $StaticJsUrl = Helper::options()->pluginUrl . '/Mou/static/js/';
        $followType = Typecho_Widget::widget('Widget_Options')->plugin('Mou')->followType;
        $expandImgType = Typecho_Widget::widget('Widget_Options')->plugin('Mou')->expandImgType;
        $expandImgBgType = Typecho_Widget::widget('Widget_Options')->plugin('Mou')->expandImgBgType;
        echo '<script type="text/javascript" src="https://cdn.staticfile.org/jquery/1.10.2/jquery.min.js"></script>';
        $mouseFollowImageDir = $dir . '/images';
        // 调试部分
        // $test = 'test';
        // echo '<script type="text/javascript" src="' . $followType . $test . 'main.js"></script>';
        echo '<script type="text/javascript" src="' . $expandImgType . $expandImgBgType . 'main.js"></script>';
        echo '<script src="https://cdn.bootcss.com/limonte-sweetalert2/7.33.1/sweetalert2.all.js"></script>';
        self::handleFollowType($followType, $expandImgType, $expandImgBgType);
        // if($expandImgType != 'default') {
        // self::handleImgExType($$expandImgType, $expandImgBgType);
        // }
        echo '<script type="text/javascript" src="' . $StaticJsUrl . 'main.js"></script>';
    }

    /*mouseFollowStyle*/
    private static function handleFollowType($followType, $expandImgType, $expandImgBgType)
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
                $(document).dblclick(function (el) {
                console.log(456)
                var elment = $(el.target)
                var tagName = elment.prop('tagName')
                if (tagName == 'IMG') {
                    imgSrc = elment.attr('src')
                    activateAlert(imgSrc)
                }
            });

            function activateAlert(imgSrc) {
                var selectedCriteria = ''
                switch (expandImgType) {
                    case 800:
                        selectedCriteria = 'moderate_enlarged'
                        break;
                    case 1000:
                        selectedCriteria = 'larger_enlarged'
                        break;
                    case 1200:
                        selectedCriteria = 'largest_enlarged'
                        break;
                    default:
                        break;
                }
                swal({
                    width: {$expandImgType},
                    padding: 20,
                    imageUrl: imgSrc,
                    imageClass: 'moderate_enlarged',
                    backdrop: '{$expandImgBgType}',
                    showConfirmButton: false,
                })
            }
            });
JS;
            $js .= '</script>';
            echo $js;
        } else {
            echo '';
        }
    }
    /*imgExpandType*/
//     private static function handleImgExType($expandImgType, $expandImgBgType)
//     {
//         $js .= '<script>';
//         $js .= <<<JS
//         $(document).ready(function () {
//             console.log(123)
            
//         });
// JS;
//         $js .= '</script>';
//         echo $js;
//     }
}
