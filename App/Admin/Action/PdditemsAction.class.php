<?php
namespace Admin\Action;
use Common\Model;
class PddItemsAction extends BaseAction {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('Pdditems');
        $this->_cate_mod = D('itemscate'); 
    }

public function clearall(){
					$sql = 'TRUNCATE TABLE '.C('DB_PREFIX').'pdditems ';
					$mes = M()->execute($sql);
					
					$file = TQK_DATA_PATH.'start_pdd.txt';
				       if(file_exists($file)){
				       file_put_contents($file, '0');		
				      }
					
					$this->ajaxReturn(1, '清空商品成功！');
}


    public function _before_index() {

        //分类信息
        $res = $this->_cate_mod->field('id,name')->select();
        $cate_list = array();
        foreach ($res as $val) {
            $cate_list[$val['id']] = $val['name'];
        }
        $this->assign('cate_list', $cate_list);
    }


 protected function _search() {
        $map = array();
        ($time_start = I('time_start','','trim')) && $map['addtime'][] = array('egt', strtotime($time_start));
        ($time_end = I('time_end','','trim')) && $map['addtime'][] = array('elt', strtotime($time_end)+(24*60*60-1));
        ($keyword = I('keyword','','trim')) && $map['goods_name'] = array('like', '%'.$keyword.'%');
        $this->assign('search', array(
            'time_start' => $time_start,
            'time_end' => $time_end,
            'keyword' => $keyword,
        ));	
        return $map;
    }


}