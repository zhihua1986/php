<?php
namespace Admin\Action;
use Common\Model;
use Common\TagLib\Dir;
class CacheAction extends BaseAction
{
    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $this->display();
    }

    public function clear() {
        $type = I('type','trim');
		//import("Org.Util.Dir");
        $obj_dir = new Dir();
        switch ($type) {
            case 'all':
                is_dir(RUNTIME_PATH) && $obj_dir->del(RUNTIME_PATH);
                break;
            case 'field':
                is_dir(DATA_PATH . '_fields/') && $obj_dir->del(DATA_PATH . '_fields/');
                break;
			case 'tpl':
                  is_dir(TEMP_PATH) && $obj_dir->delDir(TEMP_PATH);
                break;
			case 'runtime':
				is_dir(CACHE_PATH) && $obj_dir->delDir(CACHE_PATH);
                break;
            case 'data':
                is_dir(DATA_PATH) && $obj_dir->del(DATA_PATH);
                is_dir(TEMP_PATH) && $obj_dir->delDir(TEMP_PATH);
                break;
            case 'logs':
                is_dir(LOG_PATH) && $obj_dir->delDir(LOG_PATH);
                break;
        }
        
        if(C('DATA_CACHE_TYPE') == 'Memcached'){
		$handler = new \Memcached();
		$handler->addServer(C('MEMCACHED_HOST'), C('MEMCACHED_PORT'));
		$handler->flush(2);
		}
        
        $this->ajaxReturn(1,L('clear_success'));
    }

public function qclear() {
        $obj_dir = new Dir();
        is_dir(DATA_PATH . '_fields/') && $obj_dir->del(DATA_PATH . '_fields/');
		is_dir(DATA_PATH . 'data/') && $obj_dir->del(DATA_PATH . 'data/');
        is_dir(TEMP_PATH) && $obj_dir->delDir(TEMP_PATH);
		is_dir(CACHE_PATH) && $obj_dir->delDir(CACHE_PATH);
		is_dir(DATA_PATH) && $obj_dir->del(DATA_PATH);
		is_dir(LOG_PATH) && $obj_dir->delDir(LOG_PATH);
		is_dir(RUNTIME_PATH) && $obj_dir->del(RUNTIME_PATH);

		if(C('DATA_CACHE_TYPE') == 'Memcached'){
		$handler = new \Memcached();
		$handler->addServer(C('MEMCACHED_HOST'), C('MEMCACHED_PORT'));
		$handler->flush(2);
		}		
		
		
        $this->ajaxReturn(1, L('clear_success'));
    }
}