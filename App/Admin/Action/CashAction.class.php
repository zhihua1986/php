<?php
namespace Admin\Action;
use Common\Model;
class CashAction extends BaseAction
{
  //  protected $list_relation = true;
    
    public function _initialize()
    {
        parent::_initialize();
        
        $this->_name = 'usercash';
    }
    
    protected function _search()
    {
        $map = array();
        
        if($_GET['type']){
            $map['type'] = I('type','','trim');
        }
        
        if($_GET['keyword']){
         
            $map['uid'] = M('user')->where('phone ='.I('keyword','','trim'))->getfield('id');
        }
		
		if($_GET['id']){
		 
		    $map['uid'] = trim($_GET['id']);
		}
        
        return $map;
    }
    
    public function audit()
    {
        $mod = D($this->_name);
        $pk = $mod->getPk();
        
        if(IS_POST){
                
            $id = $this->_post($pk, 'intval');
                
            if (false === $data = $mod->create()) {
                IS_AJAX && $this->ajaxReturn(0, $mod->getError());
                $this->error($mod->getError());
            }
            
            if (false !== $mod->where(array('id'=>$id))->save($data)) {
                
                $item = $mod->find($id);
                
                if($item['status'] == 1){
                    M('user_cash')->add(array(
                        'uid'=>$item['uid'],
                        'money'=>$item['money'],
                        'type'=>1,
                        'remark'=>'充值：'.$item['money'].'元',
                        'data'=>$item['id'],
                        'create_time'=>NOW_TIME,
                        'status'=>1,
                    ));
                    
                    M('user')->where(array('id'=>$item['uid']))->setInc('money', $item['money']);
                }
                
                
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'audit');
                return $this->success(L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                return $this->error(L('operation_failure'));
            }
        }
        
        $id = I($pk,'','intval');
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

    public function ajax_upload_img() {
if($_FILES['img']){
	            $file = $this->_upload($_FILES['img'], 'charge',$thumb = array('width'=>150,'height'=>150));
	            if($file['error']) {
	            	$this->ajaxReturn(0,$file['info']);
	            } else {
	             $data['img']=$file['mini_pic'];
				 $this->ajaxReturn(1, L('operation_success'), $data['img']);
	            }
	   		 } else {
            $this->ajaxReturn(0, L('illegal_parameters'));
        }
    }
}