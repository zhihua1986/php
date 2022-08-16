<?php
namespace Home\Action;

use Think\Page;
use Common\Model\mtorderModel;

class MeituanAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        if ($this->visitor->is_login == false) {
            $url=U('login/index', '', '');
            redirect($url);
        }
        $this->Mod= new mtorderModel();
        $user=$this->visitor->get();
        $this->assign('user', $user);
        if ($user['tbname'] == 1) {
            $this->assign('isagent', true);
        }
    }

    public function teamorder()
    {
        $ComputingTime = abs(C('yh_wm_settle'))*86400;
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
            $list[$k]['statustxt']=$this->MettuanStatus($v['status']);
            $list[$k]['orderid']=$v['orderid'];
            $list[$k]['status']=$v['status'];
            $list[$k]['payprice']=$v['payprice'];
            $list[$k]['profit']=$v['profit'];
            $list[$k]['paytime']=$v['paytime'];
            $list[$k]['settle_time']=$v['settle_time'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['leve1']=$v['leve1'];
            $list[$k]['leve2']=$v['leve2'];
            $list[$k]['leve3']=$v['leve3'];
            $list[$k]['id']=$v['id'];
            $list[$k]['smstitle']=$v['smstitle'];
        }

        $this->assign('list', $list);

        $this->_config_seo([
            'title'=>'美团团队订单'
        ]);

        $this->display();
    }

    public function order()
    {
        $p = I('p', 1, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        $ComputingTime = abs(C('yh_wm_settle'))*86400;
        $this->assign('ComputingTime', $ComputingTime);
        $stay['uid'] = $this->memberinfo['id'];
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
            $list[$k]['statustxt']=$this->MettuanStatus($v['status']);
            $list[$k]['orderid']=$v['orderid'];
            $list[$k]['status']=$v['status'];
            $list[$k]['payprice']=$v['payprice'];
            $list[$k]['profit']=$v['profit'];
            $list[$k]['paytime']=$v['paytime'];
            $list[$k]['settle_time']=$v['settle_time'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['income']=Orebate1(['price'=>$v['profit'], 'leve'=>$v['leve1']]);
            $list[$k]['leve1']=$v['leve1'];
            $list[$k]['id']=$v['id'];
            $list[$k]['smstitle']=$v['smstitle'];
        }

        $this->assign('list', $list);
        $this->_config_seo([
            'title'=>'美团订单'
        ]);
        $this->display();
    }
}
