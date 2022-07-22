<?php
namespace Admin\Action;
use Common\Model;
class AdminAction extends BaseAction
{
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('admin');
    }

    public function _before_index() {
        $big_menu = array(
            'title' => '添加管理员',
            'iframe' => U('admin/add'),
            'id' => 'add',
            'width' => '500',
            'height' => '210'
        );
        $this->assign('big_menu', $big_menu);
        $this->list_relation = true;
    }

    public function _before_add() {
        $role_list = M('adminrole')->where('status=1')->select();
        $this->assign('role_list', $role_list);
    }

    public function _before_insert($data='') {
        if( ($data['password']=='')||(trim($data['password']=='')) ){
            unset($data['password']);
        }else{
            $data['password'] = md5($data['password']);
        }
        return $data;
    }
	
	public function addt(){
		
	        if (IS_POST) {
	        	$username=I('username','','trim');
			$password=md5(I('password','','trim'));
			$email=I('email','','trim');
			$role_id=I('role_id','','trim');	
			$data=array(
			'username'=>$username,
			'password'=>$password,
			'role_id'=>$role_id,
			'email'=>$email,
			'status'=>1
			);
	        	
			$res= $this->_mod->add($data);
			if($res){
			 IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'add');
             $this->success(L('operation_success'));		
			}else{
			 IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));	
				
			}
			
				
			}	
		
		
	}
	
	

    public function _before_edit() {
        $this->_before_add();
    }

    public function _before_update($data=''){
        if( ($data['password']=='')||(trim($data['password']=='')) ){
            unset($data['password']);
        }else{
            $data['password'] = $data['password'];
        }
        return $data;
    }

    public function ajax_check_name() {
        $name = I('J_username','','trim');
        $id = I('id','0','intval');
        if ($this->_mod->name_exists($name, $id)) {
            echo 0;
        } else {
            echo 1;
        }
    }
}