<?php
namespace Admin\Action;
use Common\Model;
class IndexAction extends BaseAction
{

    public function _initialize() {
        parent::_initialize();
		$this->_mod = D('menu');
		$NowTime=NOW_TIME;
    }
    

    public function index() {
        $top_menus = $this->_mod->admin_menu(0);
        $this->assign('top_menus', $top_menus);
        $my_admin = array('username'=>session('admin.username'), 'roleid'=>session('admin.role_id'), 'rolename'=>session('admin.role_name'));
        $this->assign('my_admin', $my_admin);
        $this->display();
    }

    public function panel() {
		
		//$Tbtime = $this->GetTbTime();
		$ServerTime = date('Y-m-d H:i:s',NOW_TIME);
		if(abs(NOW_TIME-strtotime($Tbtime))>3 && $Tbtime){
		$this->assign(
			'TimeData',array(
			'tbtime'=>$Tbtime,
			'serverTime'=>$ServerTime,
			)
		);	
			
		}
		
        $message = array();
        if (is_dir('./install')) {
            $message[] = array(
                'type' => 'Error',
                'content' => "您还没有删除 install 文件夹，出于安全的考虑，我们建议您删除 install 文件夹。",
            );
        }
        if (APP_DEBUG == true) {
            $message[] = array(
                'type' => 'Error',
                'content' => "您网站的 DEBUG 没有关闭，出于安全考虑，我们建议您关闭程序 DEBUG。",
            );
        }
        if (!function_exists("curl_getinfo")) {
            $message[] = array(
                'type' => 'Error',
                'content' => "系统不支持 CURL ,将无法采集商品数据。",
            );
        }

		$PidNum = D('pid')->where(array('status'=>1))->count('id');
		if($PidNum<20){
		$this->assign('admin_pid', true);	
		}
        $this->assign('message', $message);
		//$mysql_version=M()->Query('select VERSION()');
        $system_info = array(
            'yhxia_version' => TQK_VERSION . ' RELEASE '. TQK_RELEASE .' ',
            'server_domain' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            'server_os' => PHP_OS,
            'web_server' => $_SERVER["SERVER_SOFTWARE"],
            'php_version' => PHP_VERSION,
           // 'mysql_version' => $mysql_version['VERSION'],
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time') . '秒',
            'safe_mode' => (boolean) ini_get('safe_mode') ?  'onCorrect' : 'onError',
            'zlib' => function_exists('gzclose') ?  'onCorrect' : 'onError',
            'curl' => function_exists("curl_getinfo") ? 'onCorrect' : 'onError',
            'timezone' => function_exists("date_default_timezone_get") ? date_default_timezone_get() : L('no')
        );
 //不要删除域名否则无法获得系统更新提醒       
        $version = json_decode(file_get_contents('http://api.tuiquanke.cn/version?v=3&ver='.TQK_VERSION), true);
        if($version['result']['version'] == TQK_VERSION){
            $system_info['yhxia_version'] = $version['result']['name'];
        }
        else{
            $system_info['yhxia_version'] = '<a  href="javascript:;" style="color: #0000FF;" id="version_update">【立即更新】</a>';
        }
		 $system_info['yhxia_version'] = $system_info['yhxia_version'].'&nbsp;&nbsp;<a href="'.$version['result']['url'].'" target="_blank" style="color: #0000FF;">查看更新说明</a>';
        $count = M('balance')->where('status=0')->count();
        $this->assign('count',$count);
        $this->assign('system_info', $system_info);
	    $my_admin = array('username'=>session('admin.username'), 'roleid'=>session('admin.role_id'), 'rolename'=>session('admin.role_name'));
 
    $this->assign('my_admin', $my_admin);
		$this->assign('time',date('Y-m-d H:i'));
		$this->assign('ip',get_client_ip());
        $this->display();
    }

    public function login() {
        if (IS_POST) {
            $username = I('username', 'trim');
            $password = I('password', 'trim');
			if(!$username || !$password ){
                $this->error(L('input_empty'));
            }
            $verify_code = I('code', 'trim');
            
          if($this->check_verify($verify_code) === false && C('yh_site_status')!=0){
          	 $this->error('验证码错误');
          }
            $admin = M('admin')->where(array('username'=>$username, 'status'=>1))->find();
            if (!$admin) {
                $this->error(L('admin_not_exist'));
            }
            if ($admin['password'] != md5($password)) {
                $this->error(L('password_error'));
            }
            $admin_role = M('adminrole')->where(array('id'=>$admin['role_id']))->find();
            session('admin', array(
                'id' => $admin['id'],
                'role_id' => $admin['role_id'],
                'role_name' => $admin_role['name'],
                'username' => $admin['username'],
            ));
            
            M('admin')->where(array('id'=>$admin['id']))->save(array('last_time'=>NOW_TIME, 'last_ip'=>get_client_ip()));
            $this->success(L('login_success'), U('index/index'));
        } else {
           $this->display();
        }
    }

    public function logout() {
        session('admin', null);
        $this->success(L('logout_success'), U('index/login'));
        exit;
    }

//  public function verify_code() {
//      Image::buildImageVerify(4,1,'png','50','24');
//  }

    public function left() {
        $menuid = I('menuid','0', 'intval');
        if ($menuid && $menuid!=6){
            $left_menu = $this->_mod->admin_menu($menuid);
            foreach ($left_menu as $key=>$val) {
                $left_menu[$key]['sub'] = $this->_mod->admin_menu($val['id']);
            }
        } else {
            $left_menu[0] = array('id'=>0,'name'=>L('common_menu'));
            $left_menu[0]['sub'] = array();
            if ($r = $this->_mod->where(array('often'=>1))->select()) {
                $left_menu[0]['sub'] = $r;
            }
            array_unshift($left_menu[0]['sub'], array('id'=>0,'name'=>L('common_menu_set'),'module_name'=>'index','action_name'=>'often_menu'));
        }
        $this->assign('left_menu', $left_menu);
        $this->display();
    }

    public function often() {
        if (isset($_POST['do'])) {
            $id_arr = isset($_POST['id']) && is_array($_POST['id']) ? $_POST['id'] : '';
            $this->_mod->where(array('ofen'=>1))->save(array('often'=>0));
            $id_str = implode(',', $id_arr);
            $this->_mod->where('id IN('.$id_str.')')->save(array('often'=>1));
            $this->success(L('operation_success'));
        } else {
            $r = $this->_mod->admin_menu(0);
            $list = array();
            foreach ($r as $v) {
                $v['sub'] = $this->_mod->admin_menu($v['id']);
                foreach ($v['sub'] as $key=>$sv) {
                    $v['sub'][$key]['sub'] = $this->_mod->admin_menu($sv['id']);
                }
                $list[] = $v;
            }
            $this->assign('list', $list);
            $this->display();
        }
    }


    public function pwd() {
       $id = session('admin.id');
       $mod = M('admin');
       if(IS_POST){
       	$password=md5(I('password','','trim'));
       	$data=array(
			'password'=>$password
			);
		$res = $mod->where(array('id'=>$id))->save($data);
		if($res !== false){
		return $this->ajaxReturn(1,'修改成功！');	
		
		}else{
		return $this->ajaxReturn(0,'修改失败！');
			
		}
       	
       }
       
       
       $role_list = $mod->where(array('id'=>$id))->find();
       $this->assign('info', $role_list);
        $this->display();
    }

    public function map() {
        $r = $this->_mod->admin_menu(0);
        $list = array();
        foreach ($r as $v) {
            $v['sub'] = $this->_mod->admin_menu($v['id']);
            foreach ($v['sub'] as $key=>$sv) {
                $v['sub'][$key]['sub'] = $this->_mod->admin_menu($sv['id']);
            }
            $list[] = $v;
        }
        $this->assign('list', $list);
        $this->display();
    }
}