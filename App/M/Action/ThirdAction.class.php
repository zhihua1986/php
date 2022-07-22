<?php
namespace M\Action;

use Think\Page;
use Common\Model\dmorderModel;

class ThirdAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        if ($this->visitor->is_login == false) {
            $url=U('login/index', '', '');
            redirect($url);
        }
        $this->Mod= new dmorderModel();
        $user=$this->visitor->get();
        $this->assign('user', $user);
        if ($user['tbname'] == 1) {
            $this->assign('isagent', true);
        }
    }

    public function teamorder()
    {
        $ComputingTime = abs(C('yh_ComputingTime'))*86400;
        $this->assign('ComputingTime', $ComputingTime);
        $p = I('p', 1, 'intval');
        $uid=$this->memberinfo['id'];
        $fuid = I('fuid', 0, 'intval');
        $guid = I('guid', 0, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        if ($fuid) {
            $stay['fuid'] = $uid;
            $this->assign('fuid', $fuid);
        }

        if ($guid) {
            $stay['guid'] = $uid;
            $this->assign('guid', $guid);
        }

        if (!$guid && !$fuid) {
            $stay['fuid'] = $uid;
            $this->assign('fuid', 1);
        }
        $rows = $this->Mod->field('uid', true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $this->Mod->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $list=[];
        foreach ($rows as $k=>$v) {
            $list[$k]['statustxt']=$v['order_status'];
            $list[$k]['orderid']=$v['order_sn'];
            $list[$k]['status']=$v['status'];
            $list[$k]['payprice']=$v['orders_price'];
            $list[$k]['profit']=$v['order_commission'];
            $list[$k]['paytime']=$v['order_time'];
            $list[$k]['settle_time']=$v['charge_time'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['leve1']=$v['leve1'];
            $list[$k]['leve2']=$v['leve2'];
            $list[$k]['leve3']=$v['leve3'];
            $list[$k]['id']=$v['id'];
            $list[$k]['smstitle']=$v['goods_name'];
        }

        $this->assign('list', $list);

        $this->_config_seo([
            'title'=>'其它团队订单'
        ]);

        $this->display();
    }

    public function order()
    {
        $p = I('p', 1, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        $ComputingTime = abs(C('yh_ComputingTime'))*86400;
        $this->assign('ComputingTime', $ComputingTime);
        $stay['uid'] = $this->memberinfo['id'];
		
		$status = I('status', '0','intval');
		if($status){
		 $stay['status'] = 	$status==3?0:$status;
		}
		 $this->assign('status', $status);
		
        $rows = $this->Mod->field('leve2,leve3,uid', true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $this->Mod->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $list=[];
        $fuid=$this->memberinfo['fuid'];
        $guid=$this->memberinfo['guid'];
        foreach ($rows as $k=>$v) {
            $list[$k]['statustxt']=$v['order_status'];
            $list[$k]['orderid']=$v['order_sn'];
            $list[$k]['status']=$v['status'];
            $list[$k]['payprice']=$v['order_time'];
            $list[$k]['profit']=$v['order_commission'];
            $list[$k]['paytime']=$v['order_time'];
            $list[$k]['settle_time']=$v['charge_time'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['income']=Orebate1(['price'=>$v['order_commission'], 'leve'=>$v['leve1']]);
            $list[$k]['leve1']=$v['leve1'];
            $list[$k]['id']=$v['id'];
            $list[$k]['smstitle']=$v['goods_name'];
        }

        $this->assign('list', $list);
        $this->_config_seo([
            'title'=>'其它订单'
        ]);
        $this->display();
    }
}
