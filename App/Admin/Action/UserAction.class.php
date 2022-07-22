<?php
namespace Admin\Action;

use Common\Model;
use Common\Model\userModel;
class UserAction extends BaseAction
{
    //protected $list_relation = true;
    public function _initialize()
    {
        parent::_initialize();
        $this->_name = 'user';
    }
    protected function _before_update($data = '')
    {
        $oid = trim($data['oid']);
        if ($oid) {
            $data['oid'] = md5(substr($oid, -6, 6));
            if (M('user')->where(array('oid' => $data['oid'], 'id' => array('neq', $data['id'])))->field('id')->find()) {
                $this->ajaxReturn(0, '此订单已经被其它账号绑定');
            }
        } else {
            unset($data['oid']);
        }
        if (!$data['jd_pid']) {
            unset($data['jd_pid']);
        }
        return $data;
    }
    protected function _search()
    {
        $map = array();
        if ($keyword = I('keyword', '', 'trim')) {
            $map['phone'] = array('like', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $role = I('tbname');
        if (isset($_GET['tbname'])) {
            $map['tbname'] = 1;
            $this->assign('tbname', true);
        }
        if (isset($_GET['uid'])) {
            $map['id'] = $_GET['uid'];
            $this->assign('uid', $_GET['uid']);
        }
        return $map;
    }
    public function _before_index()
    {
        $this->sort = 'id';
        $this->order = 'DESC';
        $big_menu = array('title' => '新增用户', 'iframe' => U('user/add'), 'id' => 'add', 'width' => '500', 'height' => '200');
        $this->assign('big_menu', $big_menu);
    }
	
	public function clearOpid(){
		
		$sql = "update tqk_user set opid = '' where opid <>''";
		$res = M()->execute($sql);
		if($res){
			 $this->ajaxReturn(1, '清空OPENID成功！');
		}else{
			 $this->ajaxReturn(0, '清空OPENID失败，或者没有要清空的数据。');
		}
		
	}
	
    public function editmoney()
    {
        $id = I('id');
        $mod = new userModel();
        if (IS_POST) {
            $money = abs(I('money'));
            $charge = I('charge');
            $action = I('action');
            $remark = I('remark');
            $uid = I('id');
            if ($charge == 1 && $money > 0) {
                $res = $mod->where("id='" . $uid . "'")->setInc('money', $money);
                $msg = '系统充值';
                $type = 4;
            } else {
                $res = $mod->where("id='" . $uid . "'")->setDec('money', $money);
                $msg = '系统扣除';
                $type = 5;
            }
            if ($res) {
                M('usercash')->add(array('uid' => $uid, 'money' => $money, 'type' => $type, 'remark' => $remark ? $remark : $msg, 'create_time' => NOW_TIME, 'status' => 1));
                return $this->ajaxReturn(1, '修改成功！');
            } else {
                return $this->ajaxReturn(0, '修改失败！');
            }
        }
        $info = $mod->field('nickname,money,id')->where(array('id' => $id))->find();
        $this->assign('info', $info);
        $response = $this->fetch();
        $this->ajaxReturn(1, '', $response);
    }
    public function editscore()
    {
        $id = I('id');
        $mod = new userModel();
        if (IS_POST) {
            $money = abs(I('money'));
            $charge = I('charge');
            $action = I('action');
            $uid = I('id');
            if ($charge == 1 && $money > 0) {
                $res = $mod->where("id='" . $uid . "'")->setInc('score', $money);
                $msg = '系统充值';
                $type = 4;
            } else {
                $res = $mod->where("id='" . $uid . "'")->setDec('score', $money);
                $msg = '系统扣除';
                $type = 5;
            }
            if ($res) {
                M('basklistlogo')->add(array('uid' => $uid, 'integray' => $money, 'remark' => $msg . $money, 'create_time' => NOW_TIME, 'order_sn' => '--'));
                return $this->ajaxReturn(1, '修改成功！');
            } else {
                return $this->ajaxReturn(0, '修改失败！');
            }
        }
        $info = $mod->field('nickname,score,id')->where(array('id' => $id))->find();
        $this->assign('info', $info);
        $response = $this->fetch();
        $this->ajaxReturn(1, '', $response);
    }
    public function team()
    {
        $p = I('p', 1, 'intval');
        $page_size = 20;
        $start = $page_size * ($p - 1);
        $mod = new userModel();
        if ($_GET['tbname']) {
            $where['a.tbname'] = 1;
            $this->assign('tbname', true);
        }
        if ($_GET['fuid']) {
            $where['a.fuid'] = $_GET['fuid'];
        }
        if ($_GET['guid']) {
            $where['a.guid'] = $_GET['guid'];
        }
        if ($keyword = I('keyword', '', 'trim')) {
            $where['a.phone'] = array('like', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if (isset($_GET['uid'])) {
            $where['a.id'] = $_GET['uid'];
            $this->assign('uid', $_GET['uid']);
        }
        $field = '(SELECT count(*) FROM __MTORDER__ WHERE __MTORDER__.uid = a.id) AS mtordercount,(SELECT count(*) FROM __JDORDER__ WHERE __JDORDER__.uid = a.id) AS jdordercount,(SELECT count(*) FROM __PDDORDER__ WHERE __PDDORDER__.uid = a.id) AS pddordercount,(SELECT count(*) FROM __ORDER__ WHERE __ORDER__.uid = a.id) AS ordercount,(SELECT count(*) FROM __USER__ WHERE __USER__.fuid = a.id) AS fcount,(SELECT count(*) FROM __USER__ WHERE __USER__.guid = a.id) AS gcount,';
        $field = table_auto_format($field);
        $rows = $mod->field('a.*,' . $field . 'b.nickname as fnickname,c.nickname as gnickname')->alias('a')->join('LEFT JOIN __USER__ as b on b.id=a.fuid')->join('LEFT JOIN __USER__ as c on c.id=a.guid')->limit($start . ',' . $page_size)->where($where)->order('a.id desc')->select();
        $count = $mod->where($where)->alias('a')->count();
        $pager = new \Think\Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this->assign('page_size', $page_size);
        $this->assign('userlist', $rows);
        $this->display();
    }
    public function index()
    {
        $p = I('p', 1, 'intval');
        $page_size = 20;
        $start = $page_size * ($p - 1);
        $mod = new userModel();
        if ($_GET['tbname']) {
            $where['a.tbname'] = 1;
            $this->assign('tbname', true);
        }
        if ($_GET['webmaster']) {
            $where['a.webmaster'] = 1;
            $this->assign('webmaster', true);
        }
        $sort = I('sort');
        $order = I('order');
        if ($sort && $order) {
            $orderby = 'a.' . $sort . ' ' . $order;
        } else {
            $orderby = 'a.id desc';
        }
        if ($keyword = I('keyword', '', 'trim')) {
            $where['a.phone|a.nickname'] = array('like', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if (isset($_GET['uid'])) {
            $where['a.id'] = $_GET['uid'];
            $this->assign('uid', $_GET['uid']);
        }
        $field = '(SELECT count(*) FROM __MTORDER__ WHERE __MTORDER__.uid = a.id) AS mtordercount,(SELECT count(*) FROM __JDORDER__ WHERE __JDORDER__.uid = a.id) AS jdordercount,(SELECT count(*) FROM __PDDORDER__ WHERE __PDDORDER__.uid = a.id) AS pddordercount,(SELECT count(*) FROM __ORDER__ WHERE __ORDER__.uid = a.id) AS ordercount,(SELECT count(*) FROM __USER__ WHERE __USER__.fuid = a.id) AS fcount,(SELECT count(*) FROM __USER__ WHERE __USER__.guid = a.id) AS gcount,';
        $field = table_auto_format($field);
        $rows = $mod->field('a.*,' . $field . 'b.nickname as fnickname,c.nickname as gnickname')->alias('a')->join('LEFT JOIN __USER__ as b on b.id=a.fuid')->join('LEFT JOIN __USER__ as c on c.id=a.guid')->limit($start . ',' . $page_size)->where($where)->order($orderby)->select();
        $count = $mod->where($where)->alias('a')->count();
        $pager = new \Think\Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this->assign('page_size', $page_size);
        $this->assign('userlist', $rows);
        $this->display();
    }
    public function money()
    {
        if (IS_POST) {
        }
        $this->assign('uid', I('id', '', 'intval'));
        $this->assign('open_validator', true);
        if (IS_AJAX) {
            $response = $this->fetch();
            $this->ajaxReturn(1, '', $response);
        } else {
            $this->display();
        }
    }
    public function ajax_check_name()
    {
        $name = I('username', '', 'trim');
        $id = I('id', '', 'intval');
        $where = array('username' => $name);
        if ($id) {
            $where['id'] = array('neq', $id);
        }
        if (M('user')->where($where)->field('id')->find()) {
            $this->ajaxReturn(0, L('adboard_already_exists'));
        } else {
            $this->ajaxReturn();
        }
    }
    public function ajax_check_phone()
    {
        $phone = I('phone', '', 'trim');
        $id = I('id', '', 'intval');
        $where = array('phone' => $phone);
        if ($id) {
            $where['id'] = array('neq', $id);
        }
        if (M('user')->where($where)->field('id')->find()) {
            $this->ajaxReturn(0, L('adboard_already_exists'));
        } else {
            $this->ajaxReturn();
        }
    }
    public function edituser()
    {
        if (IS_POST) {
            $role = I('role', '', 'trim');
            $password = I('repassword', '', 'trim');
            $status = I('status', '', 'trim');
            $where = array('id' => I('id', '', 'trim'));
            $data['role'] = $role;
            $data['webmaster'] = I('webmaster', '', 'trim');
            $data['webmaster_pid'] = intval(I('webmaster_pid', '', 'trim'));
            $data['webmaster_rate'] = intval(I('webmaster_rate', '', 'trim'));
            if ($password) {
                $data['password'] = md5($password);
            }
            $data['last_time'] = time();
            $res = M('user')->where($where)->save($data);
            if ($res) {
                return $this->ajaxReturn(1, '修改成功！');
            } else {
                return $this->ajaxReturn(0, '修改失败！');
            }
        }
    }
    public function ajax_check_email()
    {
        $email = I('email', '', 'trim');
        $id = I('id', '', 'intval');
        $where = array('email' => $email);
        if ($id) {
            $where['id'] = array('neq', $id);
        }
        if (M('user')->where($where)->field('id')->find()) {
            $this->ajaxReturn(0, L('adboard_already_exists'));
        } else {
            $this->ajaxReturn();
        }
    }
    public function ajax_upload_img()
    {
        if ($_FILES['img']) {
            $file = $this->_upload($_FILES['img'], 'avatar');
            if ($file['error']) {
                $this->ajaxReturn(0, $file['info']);
            } else {
                $data['img'] = $file['pic_path'];
                $this->ajaxReturn(1, L('operation_success'), $data['img']);
            }
        } else {
            $this->ajaxReturn(0, L('illegal_parameters'));
        }
    }
}