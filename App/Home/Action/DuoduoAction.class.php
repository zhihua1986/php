<?php
namespace Home\Action;

use Think\Page;
use Common\Model\pddorderModel;

class DuoduoAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        if ($this->visitor->is_login == false) {
            $url=U('login/index', '', '');
            redirect($url);
        }
        $this->Mod= new pddorderModel();
        $user=$this->visitor->get();
        $this->assign('user', $user);
        if ($user['tbname'] == 1) {
            $this->assign('isagent', true);
        }
    }

    public function teamorder()
    {
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
        $rows = $this->Mod->field('p_id,uid', true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $this->Mod->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);

        $list=[];
        foreach ($rows as $k=>$v) {
            $list[$k]['status']=$this->pddstatus($v['status']);
            $list[$k]['order_sn']=$v['order_sn'];
            $list[$k]['order_amount']=$v['order_amount'];
            $list[$k]['add_time']=$v['add_time'];
            $list[$k]['promotion_amount']=$v['promotion_amount'];
            $list[$k]['order_pay_time']=$v['order_pay_time'];
            $list[$k]['order_settle_time']=$v['order_settle_time'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['leve1']=$v['leve1'];
            $list[$k]['leve2']=$v['leve2'];
            $list[$k]['leve3']=$v['leve3'];
            $list[$k]['id']=$v['id'];
            $list[$k]['goods_name']=$v['goods_name'];
        }

        $this->assign('list', $list);

        $this->_config_seo([
            'title'=>'团队拼多多订单'
        ]);

        $this->display();
    }

    public function duoduoorder()
    {
        $p = I('p', 1, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        $stay['uid'] = $this->memberinfo['id'];
        $rows = $this->Mod->field('p_id,leve2,leve3,uid', true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $this->Mod->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $list=[];
        $fuid=$this->memberinfo['fuid'];
        $guid=$this->memberinfo['guid'];
        foreach ($rows as $k=>$v) {
            $list[$k]['status']=$this->pddstatus($v['status']);
            $list[$k]['order_sn']=$v['order_sn'];
            $list[$k]['order_amount']=$v['order_amount'];
            $list[$k]['add_time']=$v['add_time'];
            $list[$k]['promotion_amount']=$v['promotion_amount'];
            $list[$k]['order_pay_time']=$v['order_pay_time'];
            $list[$k]['order_settle_time']=$v['order_settle_time'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['income']=Orebate1(['price'=>$v['promotion_amount'], 'leve'=>$v['leve1']]);
            $list[$k]['leve1']=$v['leve1'];
            $list[$k]['id']=$v['id'];
            $list[$k]['goods_name']=$v['goods_name'];
        }

        $this->assign('list', $list);
        $this->_config_seo([
            'title'=>'拼多多订单'
        ]);
        $this->display();
    }
}
