<?php
namespace Admin\Action;
use Common\Model;
class JditemsAction extends BaseAction {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('jditems');
    }
    

public function clearall(){
					$sql = 'TRUNCATE TABLE '.C('DB_PREFIX').'jditems ';
					$mes = M()->execute($sql);
					
						$file = TQK_DATA_PATH.'start_jd.txt';
				       if(file_exists($file)){
				       file_put_contents($file, '0');		
				      }
					
					$this->ajaxReturn(1, '清空商品成功！');
}


 protected function _search() {
        $map = array();
        ($time_start = I('time_start','','trim')) && $map['add_time'][] = array('egt', strtotime($time_start));
        ($time_end = I('time_end','','trim')) && $map['add_time'][] = array('elt', strtotime($time_end)+(24*60*60-1));
        ($keyword = I('keyword','','trim')) && $map['title'] = array('like', '%'.$keyword.'%');
        $this->assign('search', array(
            'time_start' => $time_start,
            'time_end' => $time_end,
            'keyword' => $keyword,
        ));	
        return $map;
    }


}