<?php
namespace Common\TagLib;
use Think\Template\TagLib; 
require_once(APP_PATH.'Common/TagLib/JSMin.class.php');
require_once(APP_PATH.'Common/TagLib/CSSmin.class.php');
class TagLibLoad extends TagLib {
 protected $tags   =  array(
        'tqkjs' => array('attr'=>'href', 'close'=>0),
         'tqkcss' => array('attr'=>'href', 'close'=>0),
    );
    private $jm;
    private $dir;
    function __construct() {
        $this->jm = new \JSMin();
        //$this->dir = new Dir();
    }

    public function _tqkjs($options) {
        $path = TQK_DATA_PATH . 'Runtime/' . md5($options['href']) . '.js';
        $statics_url = C('yh_statics_url') ? C('yh_statics_url') : './Public/static';
        if (!is_file($path)) {
            //静态资源地址
            $files = explode(',', $options['href']);
            $content = '';
            foreach ($files as $val) {
                $val = str_replace('__STATIC__', $statics_url, $val);
                $content.=file_get_contents($val);
            }
            file_put_contents($path, $this->jm->minify($content));
        }
		
        return ( '<script type="text/javascript" src="' . __ROOT__ . '/data/Runtime/' . md5($options['href']) . '.js?' . TQK_RELEASE . '"></script>');
    }



	public function _Tqkcss($options) {
        $path = TQK_DATA_PATH . 'Runtime/' . md5($options['href']) . '.css';
        $statics_url = C('yh_statics_url') ? C('yh_statics_url') : './Public/static';
        if (!is_file($path)) {
            $files = explode(',', $options['href']);
            $content = '';
            foreach ($files as $val) {
                $val = str_replace('__STATIC__', $statics_url, $val);
                $content.=file_get_contents($val);
            }
			 $this->Css = new \CSSmin();
            file_put_contents($path, $this->Css->run($content));
        }
       return ( '<link rel="stylesheet" type="text/css" href="' . __ROOT__ . '/data/Runtime/' . md5($options['href']) . '.css?' . TQK_RELEASE . '" />');
    }
}