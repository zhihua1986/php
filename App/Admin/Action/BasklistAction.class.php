<?php
namespace Admin\Action;
use Common\Model;
class BasklistAction extends BaseAction{
    public function _initialize(){
        parent :: _initialize();
        $this->_name = 'basklist';
        $this -> _mod = D('basklist');
		$this->assign('list_table', true);
    }
	
	
	
	public function delete_f()
    {
        $mod = D('basklistlogo');
        $pk = $mod->getPk();
        $ids = trim(I($pk), ',');
        if ($ids) {
            if (false !== $mod->delete($ids)) {
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'));
                $this->success(L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
        } else {
            IS_AJAX && $this->ajaxReturn(0, L('illegal_parameters'));
            $this->error(L('illegal_parameters'));
        }
    }

    protected function _search(){

        $map = array();
        ($time_start = I('time_start','','trim')) && $map['create_time'][] = array('egt', strtotime($time_start));
        ($time_end = I('time_end','','trim')) && $map['create_time'][] = array('elt', strtotime($time_end) + (24 * 60 * 60-1));
        ($status = I('status','','trim')) && $map['status'] = $status;
        if($status==2){
            $map['status']=0;
        }
        ($keyword = I('keyword','','trim')) && $map['content'] = array('like', '%' . $keyword . '%');
        $this -> assign('search', array('time_start' => $time_start, 'time_end' => $time_end, 'cate_id' => $cate_id, 'selected_ids' => $selected_ids, 'status' => $status, 'keyword' => $keyword,));

        return $map;
    }


public function logs(){
	
	
$p = I('p', 1, 'intval');
$page_size = 20;
$start = $page_size * ($p - 1);
$mod=M('basklistlogo');

 if($_GET['keyword']){
     $where['order_sn'] = I('keyword','','trim');
 }
 
$prefix = C(DB_PREFIX);
$field = '*,
   (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklistlogo.uid) as nickname';
$rows = $mod->field($field)->where($where)->order('id desc')->limit($start . ',' . $page_size)->select();
$count = $mod->where($where)->count();
$pager = new \Think\Page($count, $page_size);
$this->assign('page', $pager->show());
$this->assign('total_item', $count);
$this -> assign('page_size',$page_size);
$this->assign('orderlist',$rows);

	
$this->display();	
}


    public function audit()
    {
        $mod = D($this->_name);
        $pk = $mod->getPk();
        if (IS_POST) {
            if (false === $data = $mod->create()) {
                IS_AJAX && $this->ajaxReturn(0, $mod->getError());
                $this->error($mod->getError());
            }
            if($data['type'] == 1){
                $data['integray'] = C('yh_jingpintie');
            }
            if($data['type'] == 2){
                $data['integray'] = C('yh_putongtie');
            }
            if($data['status'] == 1){
                $rows = $mod->where('id='.$data['id'])->find();
                M('user')->where("id='".$rows['uid']."'")->setInc('score',$data['integray']);
                M('basklistlogo')->add(array(
                    'uid'=>$rows['uid'],
                    'integray'=>$data['integray'],
                    'remark'=>'晒单+'.$data['integray'],
                    'order_sn'=>$rows['order_sn'],
                    'create_time'=>NOW_TIME,
                    ));
            }
            if (false !== $mod->save($data)) {
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'edit');
                $this->success(L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
        } else {
            $id = I($pk, 'intval');
            $info = $mod->find($id);
            $this->assign('info', $info);
            $this->assign('open_validator', true);
            if (IS_AJAX) {
                $response = $this->fetch();
                $this->ajaxReturn(1, '', $response);
            } else {
                $this->display();
            }
        }
    }
}
?>