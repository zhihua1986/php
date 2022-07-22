<?php
namespace Admin\Action;
use Common\Model;
use Common\TagLib\Dir;
class SeoAction extends BaseAction{
	
    public function url() {
    	
        $config_file = ROOT_PATH . '/data/Config/url.php';
        $config = require $config_file;
        if (IS_POST) {
        	    $DATA_CACHE_TYPE = I('cachetype');
        	    $DATA_CACHE_TIME = I('cachetime');
        	    $MEMCACHED_HOST = I('memhost');
        	    $MEMCACHED_PORT = I('memport');
            $url_model = I('url_model','0','intval');
            $url_suffix =I('url_suffix','','trim');
            $url_depr = I('url_depr','','trim');
            $url_cache = I('url_cache','','trim');
            $url_deploy = I('url_deploy','','trim');
            $c_prefix = I('c_prefix','','trim');
            $m_prefix = I('m_prefix','','trim');
            $h_prefix = I('h_prefix','','trim');
            $url_html = I('url_html','','trim');
            if($url_cache == false){
                $obj_dir = new Dir();
                is_dir(HTML_PATH) && $obj_dir->del(HTML_PATH);
            }

            $new_config = array(
                'DATA_CACHE_TYPE' => $DATA_CACHE_TYPE,
			    'DATA_CACHE_TIME' => $DATA_CACHE_TIME,
			    'MEMCACHED_HOST'  => $MEMCACHED_HOST, 
			    'MEMCACHED_PORT' => $MEMCACHED_PORT,
			    'MEMCACHED_SERVER' => array(array($MEMCACHED_HOST,$MEMCACHED_PORT,0)),
                'CREATE_HTML_ON' => $url_html,
                'URL_MODEL' => $url_model,
                'URL_HTML_SUFFIX' => $url_suffix,
                'URL_PATHINFO_DEPR' => $url_depr,
                'APP_SUB_DOMAIN_DEPLOY' => $url_deploy,
                'HTML_CACHE_ON' => $url_cache,
                'APP_SUB_DOMAIN_RULES'  => array(
                    $c_prefix => "Home",
                    $h_prefix => "Admin",
                    $m_prefix => "M",

                    // $c_prefix => array("Home/"),
                    // $h_prefix => array("Admin/"),
                    // $m_prefix => array("Wap/"),
                    ),
                );



            if ($this->update_config($new_config, $config_file)) {
            	
            	
            	if(C('DATA_CACHE_TYPE') == 'Memcached'){
			$handler = new \Memcached();
			$handler->addServer(C('MEMCACHED_HOST'), C('MEMCACHED_PORT'));
			$handler->flush(2);
			}
            	
            	
            $this->success(L('operation_success'));
            } else {
                $this->error(L('file_no_authority'));
            }
        } else {
            $prefex = $config['APP_SUB_DOMAIN_RULES'];
            foreach ($prefex as $k => $v) {
                $config[] = $k;
            }
            $this->assign('config', $config);
            $this->display();
        }
    }

    public function page() {
        $setting_mod = D('setting');
        if (IS_POST){
            $seo_config = I('seo_config',',');
            
            $seo_config = serialize($seo_config);
            //$seo_config = json_encode($seo_config);
            
            $res=$setting_mod->where(array('name'=>'seo_config'))->save(array('data'=>$seo_config));
            if(!$res){
                $datas['name'] = 'seo_config';
                $datas['data'] = $seo_config;
                $setting_mod->save($datas);
            }
            $this->success(L('operation_success'));
        } else {
            $seo_config = $setting_mod->where(array('name'=>'seo_config'))->getField('data');
            $this->assign('seo_config', unserialize($seo_config));
           // $this->assign('seo_config', json_decode($seo_config,true));
            $this->display();
        }
    }
    
    
    
    
    
}