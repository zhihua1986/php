<?php
namespace Home\Action;

use Think\Page;
use Common\Model\userModel;
use Common\Api\Weixin;
class UserAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        if ($this->visitor->is_login == false) {
            $url=U('login/index', '', '');
            redirect($url);
        }
        $this->modorder=D('order');
        $this->artmod=D('article')->cache(true, 10 * 60);
        $user=$this->visitor->get();
		setcookie("nickname", $user['nickname'],time()+3600*24,'/');
        $this->assign('user', $user);
		if (!$user['phone'] && C('yh_index_ems')!=1) {
		    $url=U('login/fillphone', '', '');
		    redirect($url);
		}
        if ($user['tbname'] == 1) {
            $this->assign('isagent', true);
        }
    }

    public function suborder()
    {
        $mod= new userModel();
        $this->_config_seo([
            'title'=>'找回淘宝订单'
        ]);
        if (IS_POST) {
            $orderid = I('orderid');
            if (is_numeric($orderid)) {
                $UID=$this->visitor->get('id');

                $map=[
                    'id'=>$UID,
                ];
                $oid=md5(substr($orderid, -6, 6));
                $data=[
                    'oid'=>$oid
                ];

                $isset=$mod->field('id')->where($data)->find();

                if (!$isset) {
                    $res=$mod->where($map)->save($data);
                    if ($res) {
                        return $this->ajaxReturn(1, '提交成功！', U('user/suborder'));
                    }
                    $this->ajaxReturn(0, '提交失败，请检查后重试');
                } else {
                    $this->ajaxReturn(0, '此订单已经被其它账号绑定过！');
                }
            } else {
                $this->ajaxReturn(0, '提交的订单参数不符合要求');
            }
        }

        $oid=$this->visitor->get('oid');
        if ($oid) {
            $this->assign('oid', true);
        }

        $this->display();
    }

    public function channelorder()
    {
        $p = I('p', 1, 'intval');
        $status = I('status', 0, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        if ($status) {
            $stay['status'] = $status;
            $this->assign('status', $status);
        }
        $stay['relation_id'] = $this->memberinfo['webmaster_pid'];
        $stay['nstatus'] = 2;
        $rows = $this->modorder->field('goods_title,goods_num', true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $this->modorder->where($stay)->count('1');
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $list=[];
        $fuid=$this->memberinfo['fuid'];
        $guid=$this->memberinfo['guid'];
        foreach ($rows as $k=>$v) {
            $list[$k]['status']=$this->orderstatic($v['status']);
            $list[$k]['state']=$v['status'];
            $list[$k]['orderid']=$v['orderid'];
            $list[$k]['add_time']=$v['add_time'];
            $list[$k]['goods_title']=$v['goods_title'];
            $list[$k]['price']=$v['price'];
            $list[$k]['bask']=$v['bask'];
            $list[$k]['income']= round($v['income']*($this->memberinfo['webmaster_rate']/100), 2);
            $list[$k]['up_time']=$v['up_time'];
            $list[$k]['id']=$v['id'];
        }

        $this->assign('list', $list);

        $this->_config_seo([
            'title'=>'渠道订单'
        ]);
        $this->display();
    }

    public function myorder()
    {
        $ComputingTime = abs(C('yh_ComputingTime'))*86400;
        $this->assign('ComputingTime', $ComputingTime);
        $p = I('p', 1, 'intval');
        $status = I('status', 0, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        if ($status) {
            $stay['status'] = $status;
            $this->assign('status', $status);
        }

        $stay['uid'] = $this->memberinfo['id'];
        //$stay['nstatus'] = 1;
        $rows = $this->modorder->field('goods_num', true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $this->modorder->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $list=[];
        $fuid=$this->memberinfo['fuid'];
        $guid=$this->memberinfo['guid'];
        foreach ($rows as $k=>$v) {
            $list[$k]['status']=$this->orderstatic($v['status']);
            $list[$k]['state']=$v['status'];
            $list[$k]['orderid']=$v['orderid'];
            $list[$k]['add_time']=$v['add_time'];
            $list[$k]['goods_title']=$v['goods_title'];
            $list[$k]['price']=$v['price'];
            $list[$k]['bask']=$v['bask'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['income']=Orebate1(['price'=>$v['income'], 'leve'=>$v['leve1']]);
            $list[$k]['up_time']=$v['up_time'];
            $list[$k]['id']=$v['id'];
        }

        $this->assign('list', $list);
        $this->_config_seo([
            'title'=>'我的订单'
        ]);
        $this->display();
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
        //$stay['nstatus'] = 1;
        $rows = $this->modorder->field('status,orderid,goods_title,add_time,price,income,settle,up_time,id,leve1,leve2,leve3')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
//
        $count = $this->modorder->where($stay)->count();

        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);

        $list=[];
        foreach ($rows as $k=>$v) {
            $list[$k]['status']=$this->orderstatic($v['status']);
            $list[$k]['state']=$v['status'];
            $list[$k]['orderid']=$v['orderid'];
            $list[$k]['add_time']=$v['add_time'];
            $list[$k]['price']=$v['price'];
            $list[$k]['income']=$v['income'];
            $list[$k]['goods_title']=$v['goods_title'];
            $list[$k]['up_time']=$v['up_time'];
            $list[$k]['id']=$v['id'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['leve1']=$v['leve1'];
            $list[$k]['leve2']=$v['leve2'];
            $list[$k]['leve3']=$v['leve3'];
        }

        $this->assign('list', $list);

        $this->_config_seo([
            'title'=>'团队订单'
        ]);

        $this->display();
    }

    public function myteam()
    {
        $usermod=M('user');
        $uid=$this->memberinfo['id'];
        $today_str = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
        $last_str = mktime(0, 0, 0, date("m"), date("d")-2, date("Y"));
        $end =date('Y-m-01', strtotime(date("Y-m-d")));
        $end = strtotime($end);
        $field = 'id,
        ( select count(id) from tqk_user where fuid = '.$uid.' or guid = '.$uid.') as allperson,
        ( select count(id) from tqk_user where (fuid = '.$uid.' or guid = '.$uid.') and reg_time>'.$today_str.' ) as todayperson,
        ( select count(id) from tqk_user where (fuid = '.$uid.' or guid = '.$uid.') and (reg_time>'.$last_str.' and reg_time<'.$today_str.') ) as lastperson,
        	( select count(id) from tqk_user where (fuid = '.$uid.' or guid = '.$uid.') and reg_time>'.$end.' ) as thismonthperson,
        ( select count(id) from tqk_user where fuid = '.$uid.') as person1,
        ( select count(id) from tqk_user where guid = '.$uid.') as person2';
        $res = $usermod->field($field)->find();
        $this->assign('stat', $res);

        $p = I('p', 1, 'intval');
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

        $rows = $usermod->field('reg_time,nickname,oid')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $usermod->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this->assign('info', $rows);
        $this -> assign('page_size', $page_size);

        $this->_config_seo([
            'title'=>'我的团队'
        ]);

        $this->display();
    }

    public function teamprofit()
    {
        $modfin=M('balance');
        $uid=$this->memberinfo['id'];
        $fuid=$this->memberinfo['fuid'];
        $guid=$this->memberinfo['guid'];
        $today_str = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $tomorr_str = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $pre_time = strtotime(date('Y-m-01', strtotime('last day of -1 month')));
        $last_str = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
        $pre_month=$this->getthemonth($pre_time);
        $field = 'id,
( select SUM(money) from tqk_usercash where uid = '.$uid.' and type=3 and create_time>'.$pre_month[1].') as allmoney,
( select SUM(income*leve1/100) from tqk_order where nstatus=1 and (status =1 or status =3) and uid = '.$uid.' and add_time>'.$today_str.') as u_today_money,
( select SUM(income*leve2/100) from tqk_order where nstatus=1 and (status =1 or status =3) and fuid = '.$uid.' and add_time>'.$today_str.') as f_today_money,
( select SUM(income*leve3/100) from tqk_order where nstatus=1 and (status =1 or status =3) and guid = '.$uid.' and add_time>'.$today_str.') as g_today_money,
( select SUM(promotion_amount*leve1/100) from tqk_pddorder where status <>4 and uid = '.$uid.' and order_pay_time>'.$today_str.') as udd_today_money,
( select SUM(promotion_amount*leve2/100) from tqk_pddorder where status <>4 and fuid = '.$uid.' and order_pay_time>'.$today_str.') as fdd_today_money,
( select SUM(promotion_amount*leve3/100) from tqk_pddorder where status <>4 and guid = '.$uid.' and order_pay_time>'.$today_str.') as gdd_today_money,
( select SUM(estimateFee*leve1/100) from tqk_jdorder where validCode >1  and uid = '.$uid.' and orderTime>'.$today_str.') as ujd_today_money,
( select SUM(estimateFee*leve2/100) from tqk_jdorder where validCode >1 and fuid = '.$uid.' and orderTime>'.$today_str.') as fjd_today_money,
( select SUM(estimateFee*leve3/100) from tqk_jdorder where validCode >1 and guid = '.$uid.' and orderTime>'.$today_str.') as gjd_today_money,
( select SUM(profit*leve1/100) from tqk_mtorder where status<9 and uid = '.$uid.' and paytime>'.$today_str.') as umt_today_money,
( select SUM(profit*leve2/100) from tqk_mtorder where status<9 and fuid = '.$uid.' and paytime>'.$today_str.') as fmt_today_money,
( select SUM(profit*leve3/100) from tqk_mtorder where status<9 and guid = '.$uid.' and paytime>'.$today_str.') as gmt_today_money,
( select SUM(order_commission*leve1/100) from tqk_dmorder where status>=0 and uid = '.$uid.' and order_time>'.$today_str.') as udm_today_money,
( select SUM(order_commission*leve2/100) from tqk_dmorder where status>=0 and fuid = '.$uid.' and order_time>'.$today_str.') as fdm_today_money,
( select SUM(order_commission*leve3/100) from tqk_dmorder where status>=0 and guid = '.$uid.' and order_time>'.$today_str.') as gdm_today_money,
( select SUM(income*leve1/100) from tqk_order where nstatus=1 and (status =1 or status =3) and uid = '.$uid.' and  add_time>'.$pre_month[1].') as u_month,
( select SUM(income*leve2/100) from tqk_order where nstatus=1 and (status =1 or status =3) and fuid = '.$uid.' and add_time>'.$pre_month[1].') as f_month,
( select SUM(income*leve3/100) from tqk_order where nstatus=1 and (status =1 or status =3) and guid = '.$uid.' and add_time>'.$pre_month[1].') as g_month,
( select SUM(promotion_amount*leve1/100) from tqk_pddorder where status <>4 and uid = '.$uid.' and order_settle_time>'.$pre_month[1].') as udd_month,
( select SUM(promotion_amount*leve2/100) from tqk_pddorder where status <>4 and fuid = '.$uid.' and order_settle_time>'.$pre_month[1].') as fdd_month,
( select SUM(promotion_amount*leve3/100) from tqk_pddorder where status <>4 and guid = '.$uid.' and order_settle_time>'.$pre_month[1].') as gdd_month,
( select SUM(estimateFee*leve1/100) from tqk_jdorder where validCode >1 and uid = '.$uid.' and orderTime>'.$pre_month[1].') as ujd_month,
( select SUM(estimateFee*leve2/100) from tqk_jdorder where validCode >1 and fuid = '.$uid.' and orderTime>'.$pre_month[1].') as fjd_month,
( select SUM(estimateFee*leve3/100) from tqk_jdorder where validCode >1 and guid = '.$uid.' and orderTime>'.$pre_month[1].') as gjd_month,
( select SUM(profit*leve1/100) from tqk_mtorder where status<9 and uid = '.$uid.' and settle_time>'.$pre_month[1].') as umt_month,
( select SUM(profit*leve2/100) from tqk_mtorder where status<9 and fuid = '.$uid.' and settle_time>'.$pre_month[1].') as fmt_month,
( select SUM(profit*leve3/100) from tqk_mtorder where status<9 and guid = '.$uid.' and settle_time>'.$pre_month[1].') as gmt_month,
( select SUM(order_commission*leve1/100) from tqk_dmorder where status>=0 and uid = '.$uid.' and order_time>'.$pre_month[1].') as udm_month,
( select SUM(order_commission*leve2/100) from tqk_dmorder where status>=0 and fuid = '.$uid.' and order_time>'.$pre_month[1].') as fdm_month,
( select SUM(order_commission*leve3/100) from tqk_dmorder where status>=0 and guid = '.$uid.' and order_time>'.$pre_month[1].') as gdm_month,
( select count(*) from tqk_pddorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and order_pay_time>'.$today_str.') as dd_today_count,
( select count(*) from tqk_pddorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and order_pay_time>'.$pre_month[1].' ) as dd_this_month_count,
( select count(*) from tqk_pddorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (order_pay_time>'.$pre_month[0].' and order_pay_time<'.$pre_month[1].') ) as dd_last_month_count,
( select count(*) from tqk_jdorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and orderTime>'.$today_str.') as jd_today_count,
( select count(*) from tqk_jdorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and orderTime>'.$pre_month[1].' ) as jd_this_month_count,
( select count(*) from tqk_jdorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (orderTime>'.$pre_month[0].' and orderTime<'.$pre_month[1].') ) as jd_last_month_count,
( select count(*) from tqk_mtorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and paytime>'.$today_str.') as mt_today_count,
( select count(*) from tqk_mtorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and paytime>'.$pre_month[1].' ) as mt_this_month_count,
( select count(*) from tqk_mtorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (paytime>'.$pre_month[0].' and paytime<'.$pre_month[1].') ) as mt_last_month_count,
( select count(*) from tqk_dmorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and order_time>'.$today_str.') as dm_today_count,
( select count(*) from tqk_dmorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and order_time>'.$pre_month[1].' ) as dm_this_month_count,
( select count(*) from tqk_dmorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (order_time>'.$pre_month[0].' and order_time<'.$pre_month[1].') ) as dm_last_month_count,
( select count(*) from tqk_order where nstatus=1 and (status =1 or status =3) and (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and add_time>'.$today_str.') as today_count,
( select count(*) from tqk_order where nstatus=1 and (status =1 or status =3) and (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and add_time>'.$pre_month[1].' ) as this_month_count,
( select count(*) from tqk_order where nstatus=1 and (status =1 or status =3) and (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (add_time>'.$pre_month[0].' and add_time<'.$pre_month[1].') ) as last_month_count';
        $res = D('user')->field($field)->order('id desc')->find();
        if ($res) {
            $allmoney=$res['allmoney'];
            $this->assign('allmoney', $allmoney);
            $yesterday_money=round($res['udm_today_money']+$res['gdm_today_money']+$res['fdm_today_money']+$res['u_today_money']+$res['udd_today_money']+$res['ujd_today_money']+$res['f_today_money']+$res['fdd_today_money']+$res['umt_today_money']+$res['gmt_today_money']+$res['fmt_today_money']+$res['fjd_today_money']+$res['g_today_money']+$res['gdd_today_money']+$res['gjd_today_money'], 2);
            $this->assign('yesterday_money', $yesterday_money);
            $this_month=round($res['gdm_month']+$res['fdm_month']+$res['udm_month']+$res['u_month']+$res['udd_month']+$res['ujd_month']+$res['gmt_month']+$res['fmt_month']+$res['umt_month']+$res['f_month']+$res['fdd_month']+$res['fjd_month']+$res['g_month']+$res['gdd_month']+$res['gjd_month'], 2);
            $this->assign('this_month', $this_month);
        }
        $this->assign('stat', $res);

        $p = I('p', 1, 'intval');
        $page_size = 6;
        $start = $page_size * ($p - 1);
        $stay['uid'] = $uid;
        $stay['status'] = 1;
        $rows = $modfin->field('id,money,create_time')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $modfin->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $this->assign('list', $rows);
        $this->_config_seo([
            'title'=>'我的收益'
        ]);
        $this->display();
    }

    public function vieworder()
    {
        $this->_config_seo([
            'title' => '帮助中心'
        ]);
        $helps =M('help')->field('id,title')->select();
        $this->assign('helps', $helps);
        $this->display();
    }

    public function bindtb()
    {
        $time=NOW_TIME;
        $map=[
            'time'=>$time,
            'tqk_uid'=>$this->tqkuid,
            'uid'=>$this->visitor->get('id'),
            'web'=>'pc'
        ];
        $token=$this->create_token(trim(C('yh_gongju')), $map);
        $url='http://api.tuiquanke.cn/bindtaobao?time='.$time.'&tqk_uid='.$this->tqkuid.'&uid='.$this->visitor->get('id').'&web=pc&token='.$token;
        echo('<script>window.location.href="'.$url.'" </script>');
    }

    public function fillphone()
    {
        if (IS_POST) {
            $phone = I('phone');

            $exist = M('user')->where([
                'phone' => $phone
            ])->count('id');

            if ($exist) {
                return $this->ajaxReturn(0, '手机号已存在！');
            }

            $data['phone'] = $phone;
            $res=M('user')->where('id='.$this->visitor->get('id'))->save($data);
            if ($res) {
                return $this->ajaxReturn(1, '成功保存！');
            }
            return $this->ajaxReturn(0, '保存失败！');
        }

        $this->display();
    }

    public function ucenter()
    {
        $NowTime=NOW_TIME;
        $article=$this->artmod->where('cate_id=2')->field('id,title')->limit(5)->select();
        $this->assign('article', $article);
        $uid=$this->visitor->get('id');
        if (!$uid) {
            $url=U('login/index', '', '');
            redirect($url);
        }

        if ($uid>0) {
            $code=$this->visitor->get('invocode');
            if (!$code) {
                $codes=$this->invicode($uid);
                $this->assign('invicode', $codes);
            }
        }
		
		// if($this->visitor->get('special_id') < 2 ){
		// $this->Getspecial();
		// }
		
        $tbname=$this->visitor->get('tbname');
        if (1 != $tbname) {
            $map=[
                'uid'=>$uid,
                'type'=>3
            ];
            $mymoney=M('usercash')->where($map)->sum('amount');
            $agentcondition=trim(C('yh_agentcondition'));
            if (!$mymoney) {
                $mymoney=0;
            }
            if ($mymoney>=$agentcondition) {
                M('user')->where(['id'=>$uid])->setField('tbname', 1);
            }
        }

        $where=[
            'oid'=>$this->visitor->get('oid'),
            'uid'=> $uid,
        ];
        $integral=$this->modorder->where($where)->count("id");
        $this->assign('integral', $integral ? $integral : 0);
        $stay['oid'] = $this->visitor->get('oid');
        $stay['uid'] = $this->memberinfo['id'];
        $rows = $this->modorder->where($stay)->order('id desc')->limit(10)->select();
        $list=[];
        foreach ($rows as $k=>$v) {
            $list[$k]['status']=$this->orderstatic($v['status']);
            $list[$k]['orderid']=$v['orderid'];
            $list[$k]['add_time']=$v['add_time'];
            $list[$k]['price']='￥'.$v['price'];
            $list[$k]['integral']='￥'.Orebate1(['price'=>$v['income'], 'leve'=>$v['leve1']]);
            $list[$k]['up_time']=$v['up_time'];
        }

        $this->assign('list', $list);

        $this->_config_seo([
            'title'=>'用户中心'
        ]);
        $this->display();
    }

    protected function orderstatic($id)
    {
        switch ($id) {
    case 0:
    return '待处理';
    break;
    case 1:
    return '已付款';
    break;
    case 2:
    return '无效订单';
    break;
    case 3:
    return '已收货';
    break;
    default:
    return '订单失效';
    break;
}
    }

    public function order()
    {
        $p = I('p', 1, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        $stay['oid'] = $this->visitor->get('oid');
        $stay['uid'] = $this->memberinfo['id'];
        $stay['nstatus'] = 0;
        $rows = $this->modorder->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $this->modorder->where($stay)->count();
        $pager = new Page($count, $size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $list=[];
        foreach ($rows as $k=>$v) {
            $list[$k]['status']=$this->orderstatic($v['status']);
            $list[$k]['orderid']=$v['orderid'];
            $list[$k]['state']=$v['status'];
            $list[$k]['add_time']=$v['add_time'];
            $list[$k]['price']="￥".$v['price'];
            $list[$k]['integral']=$v['integral'];
            $list[$k]['up_time']=$v['up_time'];
            $list[$k]['id']=$v['id'];
        }

        $this->assign('list', $list);

        $where=[
            'oid'=>$this->visitor->get('oid'),
            'uid'=>$this->memberinfo['id'],
            'status'=>1,
            'nstatus'=>0
        ];

        $integral=$this->modorder->where($where)->sum('integral');

        $this->assign('integral', $integral ? $integral : 0);

        $this->_config_seo([
            'title'=>'我的订单'
        ]);

        $this->display();
    }

    public function jifen()
    {
        if (IS_POST) {
            $count = abs(I('count'));
            if ($count<1 || is_float($count)) {
                $this->ajaxReturn(0, '兑换数量必须是1的整数倍');
            }
            $user=M('user')->where('id='.$this->memberinfo['id'])->field('id,score,money')->find();

            if ($count>$user['score']) {
                $this->ajaxReturn(0, '兑换数量与实际数量不符');
            }

            $userid=$this->memberinfo['id'];
            $price=(C('yh_fanxian')/100)*$count;

            if ($price>0) {
                M('user')->where('id='.$userid)->save([
                    'money'=>['exp', 'money+'.$price],
                    'score'=>['exp', 'score-'.$count]
                ]);

                M('usercash')->add([
                    'uid'         =>$userid,
                    'money'       =>$price,
                    'remark'      =>'积分兑换: '.$price.'元',
                    'type'        =>10,
                    'create_time' =>time(),
                    'status'      =>1,
                ]);
                $this->ajaxReturn(1, '兑换成功！');
            } else {
                $this->ajaxReturn(0, '兑换失败！');
            }
        }

        $this->_config_seo([
            'title'=>'积分兑换'
        ]);

        $this->display();
    }

    public function index()
    {
        $callback=I('callback', '', 'trim');
        $where['openid'] = $this->openid;
        $user = M('user')->where($where)->field('nickname,avatar,money,score,openid')->find();
        if ($user) {
            $this->assign('user', $user);
        } else {
            exit('您的账号不存在！');
        }
        $this->_config_seo([
            'title'=>'用户中心'
        ]);
        $this->display();
    }

    public function modify()
    {
        $F=M('user');
        if (IS_POST) {
            $password = I('password');
            $password2 = I('password2');
            $phone = I('phone');
            $data=[
                'nickname'=>I('nickname'),
                //				'username'=>$this->_param('username','trim'),
                'qq'=>I('qq'),
                'wechat'=>I('wechat'),
            ];

            if ($phone) {
                $result = $F->where(['phone'=>$phone])->count('id');
                if ($result) {
                    $this->ajaxReturn(0, '手机号已存在');
                } else {
                    $data['phone'] = $phone;
                }
            }

            if (I('avatar')) {
                $data['avatar'] = I('avatar');
            }
            if ($password) {
                if ($password == $password2) {
                    $data['password'] = md5($password);
                } else {
                    $this->ajaxReturn(0, '两次密码不一致');
                }
            }
            $where['id'] = $this->visitor->get('id');
            $res = $F->where($where)->save($data);

            if ($res !== false) {
                return $this->ajaxReturn(1, '修改成功');
            }
            $this->ajaxReturn(0, '修改失败');
        }
        $where['id'] = $this->visitor->get('id');
        $info = $F->where($where)->field('nickname,avatar,username,qq,wechat,phone')->find();
        $this->assign('info', $info);
        $this->_config_seo([
            'title'=>'修改资料'
        ]);
        $this->display();
    }

    public function uploadimg()
    {
        if (IS_POST) {
            if ($_FILES['file']) {
                $file = $this->_upload($_FILES['file'], 'avatar', $thumb = ['width'=>180, 'height'=>180]);
                if ($file['error']) {
                    $this->ajaxReturn(0, $file['info']);
                } else {
                    $this->ajaxReturn(1, $file['mini_pic']);
                }
            }
        }
    }

    public function tixian()
    {
        $mod=new userModel();
		C('TOKEN_ON',true);//开启令牌验证
		
		if(!$this->visitor->get('phone')){
		//	$url=U('login/fillphone', '', '');
		//	$this->error('绑定手机号后才可以提现哟！',$url);
			// redirect($url);
		}
		
        if (IS_POST) {
            $F=M('balance');
			
			if(!$this->tqkCheckToken($_POST)){
			    $this->ajaxReturn(0, '请不要多次重复提交！');
			    exit;
			  }
			
            $mymoney = abs(I('money'));
            if ($mymoney<=0) {
                $this->ajaxReturn(0, '提现金额异常！');
                exit;
            }
            if ($mymoney<C('yh_Quota')) {
                $this->ajaxReturn(0, '单笔提现金额不能小于'.C('yh_Quota').'元');
                exit;
            }
            $map['id'] = $this->visitor->get('id');
            $balance = $mod->field('nickname,avatar,username,money,id,realname,alipay')->where($map)->find();
            $alipay=I('allpay');
            $name=I('name');
			
			if(!$alipay && I('method') == '2'){
				$this->ajaxReturn(0, '支付宝账号和姓名必须要填写！');
				exit;
			}
			
			
            if ($alipay && $name && !$balance['alipay']) {
                $data=[
                    'realname'=>$name,
                    'alipay'=>$alipay
                ];
                $mod->where($map)->save($data);
            }

            if ($mymoney>$balance['money']) {
                $this->ajaxReturn(0, '账户余额不足！');
                exit();
            }
            $data=[
                'uid'=>$balance['id'],
                'money'=>$mymoney,
                'name'=>$name ? $name : $balance['realname'],
                'method'=>I('method')?:2,
                'allpay'=>$alipay ? $alipay : $balance['alipay'],
                'status'=>0,
                'content'=>I('content'),
                'create_time'=>time()
            ];
            $res = $F->add($data);
            if ($res !== false) {
				$resid = $res;
                $mod->where([
                    'id'=>$balance['id']
                ])->save([
                    'money'=>['exp', 'money-'.$mymoney],
                    'frozen'=>['exp', 'frozen+'.$mymoney],
                ]);

                M('usercash')->add([
                    'uid'=>$balance['id'],
                    'money'=>$mymoney,
                    'type'=>1,
                    'remark'=>'提现冻结资金：'.$mymoney.'元',
                    'create_time'=>NOW_TIME,
                    'status'=>1,
                ]);

                $myphone=trim(C('yh_sms_my_phone'));
                if (0 < C('yh_sms_status') && $myphone) {
                    $data=[
                        'phone'=>$myphone,
                        'code'=>$this->visitor->get('nickname'),
                        'webname'=>(float) $mymoney,
                        'temp_id'=>trim(C('yh_sms_tixian_id'))
                    ];
                    $res= $this->send_sms($data);
                }
				
				$data = array(
				'name'=>$this->visitor->get('nickname'),
				'money'=>(float) $mymoney,
				'payment'=> $this->CashMethod(I('method')),
				'content'=>I('content')
				);
				
				Weixin::Cashout($data);
				
				if(C('yh_payment') == '2' && I('method') == '1'){
				
				 $res = $this->wechatpay($balance['id'],$mymoney,$resid);
				 if($res['code']==200){
					 $this->ajaxReturn(1, $res['msg']); 
				 }
				 $this->ajaxReturn(0, $res['msg']);
				
				}
				

                return $this->ajaxReturn(1, '申请提交成功，请等待处理！');
            }
            $this->ajaxReturn(0, '申请提交失败！');
        }

        $where['id'] = $this->visitor->get('id');
        $user = $mod->where($where)->field('nickname,avatar,username,money,realname,alipay')->find();
        $this->assign('user', $user);
        $this->_config_seo([
            'title'=>'我要提现'
        ]);
        $this->display();
    }

    public function journal()
    {
        $p = I('p', 1, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        $where['id'] = $this->visitor->get('id');
        $user = M('user')->where($where)->field('nickname,avatar,username,money,frozen,id')->find();
        if ($user) {
            $this->assign('user', $user);
        } else {
            exit('您的账号不存在！');
        }

        $where=[
            'type'=>6,
            'uid'=>$user['id']
        ];
        $balance = M('usercash')->where($where)->sum('money');
        if ($balance) {
            $this->assign('balance', $balance);
        } else {
            $this->assign('balance', 0);
        }
        $stay['uid'] = $user['id'];
        $rows = M('usercash')->where($stay)->field('type,money,create_time,status,remark')->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = M('usercash')->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $list=[];
        foreach ($rows as $k=>$v) {
            $val=unserialize(user_cash_type($v['type']));
            $list[$k]['create_time']=$v['create_time'];
            $list[$k]['type']=$val[0];
            $list[$k]['remark']=$v['remark'];
            $list[$k]['money']=$val[1].$v['money'];
        }
        $this->assign('info', $list);
        $this->_config_seo([
            'title'=>'财务日志'
        ]);
        $this->display();
    }

    public function record()
    {
        $p = I('p', 1, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        $where['id'] = $this->visitor->get('id');
        $user = M('user')->where($where)->field('frozen,nickname,avatar,money,score,openid')->find();
        if ($user) {
            $this->assign('user', $user);
        } else {
            exit('您的账号不存在！');
        }
        $map['uid'] = $this->visitor->get('id');
        $rows = M('balance')->where($map)->field('money,create_time,status')->limit($start . ',' . $page_size)->order('id desc')->select();
        $count = M('balance')->where($map)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $this->assign('info', $rows);
        $this->_config_seo([
            'title'=>'我的钱包'
        ]);
        $this->display();
    }
}
