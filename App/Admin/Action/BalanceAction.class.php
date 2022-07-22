<?php
namespace Admin\Action;

class BalanceAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();

        $this->_name = 'balance';
    }

    protected function _search()
    {
        $map = [];

        //$map['type'] = $this->_get('type', 'trim', 'main');

        return $map;
    }

    public function _before_insert()
    {
        $data['sn'] = date('YmdHis').rand(10000, 99999);
        $data['confirm'] = 0;
        $data['create_time']=NOW_TIME;
        $data['status']=0;

        return $data;
    }

    public function balancedelete()
    {
        $id    = I('id');
        if (!$id) {
            $this->ajaxReturn(0, '操作失败，请稍后再试');
        }
        $row = M('balance')->where(['id'=>$id, 'status'=>0])->find();

        if ($row) {
            $money = $row['money'];

            $map['id'] = $row['uid'];
            $data = [
                'money'=>['exp', 'money+'.$money],
                'frozen'=>['exp', 'frozen-'.$money],
            ];
            $row1 = M('user')->where($map)->save($data);
			if($row1){
            $row2 = M('usercash')->add([
                'uid'         =>$row['uid'],
                'money'       =>$money,
                'remark'      =>'提现失败退款 : '.$money.'元',
                'type'        =>4,
                'create_time' =>time(),
                'status'      =>1,
            ]);
			}
                M('balance')->where(['id'=>$id, 'status'=>0])->delete();
                return 	$this->ajaxReturn(1, '删除成功');
        }
    }

    public function balance_status()
    {
        $id    = I('id');
        $money = I('money', '', 'trim');
        $uid   = I('uid', '', 'trim');
		$method = I('method');
        if (!$id) {
            $this->ajaxReturn(0, '操作失败，请稍后再试');
        }
		$status = I('status', 0);
		
		$info = M('balance')->where(array('id'=>$id))->find();
		
		if(C('yh_payment_wechat') == '1' && $info['method'] == '1'){
		
		 $res = $this->wechatpay($uid,$info['money'],$id);
		 if($res['code']==200){
			 $this->ajaxReturn(1, $res['msg']); 
		 }
		 $this->ajaxReturn(0, $res['msg']);
		
		}
		
       
        $row = M('balance')->where(['id'=>$id])->save(['status'=>$status]);
        if ($row) {
            $map['id'] = $uid;
            $data = [
                'frozen'=>['exp', 'frozen-'.$info['money']],
            ];
            $row1 = M('user')->where($map)->save($data);
            $row2 = M('usercash')->add([
                'uid'         =>$uid,
                'money'       =>$info['money'],
                'remark'      =>'余额提现 : '.$info['money'].'元',
                'type'        =>6,
                'create_time' =>time(),
                'status'      =>1,
            ]);
            if ($row2 && $row1) {
                return 	$this->ajaxReturn(1, '操作成功');
            }
        }
		
    }

    public function ajax_upload_img()
    {
        if ($_FILES['img']) {
            $file = $this->_upload($_FILES['img'], 'charge', $thumb = ['width'=>150, 'height'=>150]);
            if ($file['error']) {
                $this->ajaxReturn(0, $file['info']);
            } else {
                $data['img']=$file['mini_pic'];
                $this->ajaxReturn(1, L('operation_success'), $data['img']);
            }
        } else {
            $this->ajaxReturn(0, L('illegal_parameters'));
        }
    }
	
	
	
	
}
