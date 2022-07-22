<?php
namespace Admin\Action;
use Common\Model;
class FinanceAction extends BaseAction
{

	public function _initialize()
	{
		parent::_initialize();

		$this->_name = 'finance';
		 $this->assign('list_table', true);
		$this->_mod = D('finance');
	}
	
	
public function pddlist(){
	
$p = I('p', 1, 'intval');
$page_size = 20;
$start = $page_size * ($p - 1);
$mod=M($this->_name);
if($_GET['status']){
$where['status']=$this->_get('status', 'trim');
 } 
 if($_GET['keyword']){
     $where['uid'] = M('user')->where('phone ='.I('keyword','','trim'))->getfield('id');
 }
 
 if($_GET['time_start']){
$where['add_time']=I('time_start','','trim');
 } 
 $where['type']=2;
 
$prefix = C(DB_PREFIX);
$field = '*,
   (select phone from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'finance.uid limit 1) as phone,
   (select name from '.$prefix.'apply where '.$prefix.'apply.uid = '.$prefix.'finance.uid limit 1) as name,
   (select alipay from '.$prefix.'apply where '.$prefix.'apply.uid = '.$prefix.'finance.uid limit 1) as alipay';
$rows = $mod->field($field)->where($where)->order('id desc')->limit($start . ',' . $page_size)->select();
$count = $mod->where($where)->count();
$pager = new \Think\Page($count, $page_size);
$this->assign('page', $pager->show());
$this->assign('total_item', $count);
$this -> assign('page_size',$page_size);
foreach($rows as $k=>$v){
		$list[$k]['status']=$this->orderstatic($v['status']);
		$list[$k]['status_t']=$v['status'];
		$list[$k]['price']='￥'.$v['price'];
		$list[$k]['mark']=$v['mark'];
		$list[$k]['name']=$v['name'];
		$list[$k]['alipay']=$v['alipay'];
		$list[$k]['income']='￥'.$v['income'];
		$list[$k]['add_time']=$v['add_time'];
		$list[$k]['phone']=$v['phone'];
		$list[$k]['id']=$v['id'];
		$list[$k]['backcash']='￥'.$v['backcash'];
		
}

$this->assign('orderlist',$list);

$this->display();
		
		
}


public function tuiquanke_collect($startpage=1,$page_size=5){
$moduser=M('user');
$modfin=M('finance');
$modorder=M('order');
$modusercash=M('usercash');
$startlimit = $page_size * ($startpage - 1);
$rows = $moduser->field('oid,id,guid,fuid,tbname,webmaster,webmaster_pid,webmaster_rate')->where('status=1')->order('id desc')->limit($startlimit . ',' . $page_size)->select();

$count = $moduser->where('status=1')->count();

$BeginDate=date('Y-m-01', strtotime('last day of -1 month'));	
$start=strtotime($BeginDate);
$end =date('Y-m-01', strtotime(date("Y-m-d")));
$end = strtotime($end);
$map['up_time'] = array(
            array(
                'egt',
                $start
            ),
            array(
                'elt',
                $end
            )
);	
$map['status']=3;
$map['nstatus']=1;
$map['add_time'] = array('egt',1559318400);

if($rows){
$msg=0;

foreach($rows as $k=>$v){

$uid=$v['id'];
$fuid=$v['fuid'];
$guid=$v['guid'];
$money=0;
$leve1_stat=0;
$leve2_stat=0;
$leve3_stat=0;
$leve4_stat=0;
$leve1_list=array();
$leve2_list=array();
$leve3_list=array();
$leve4_list=array();
if($v['tbname']==1 or $v['webmaster']==1){

$where_leve1['_complex'] = $map;
$where_leve1['uid']=$uid;

$leve1_list=$modorder->field('id,uid,fuid,guid,income,leve1,leve2,leve3')->where($where_leve1)->order('id desc')->select();



foreach($leve1_list as $k1=>$v1){
$leve1_list[$k1]['income']=Orebate1(array('price'=>$v1['income'],'leve'=>$v1['leve1']));
$leve1_stat=$leve1_stat+$leve1_list[$k1]['income']; //my
}


$where_leve2['_complex'] = $map;
$where_leve2['fuid']=$uid;

$leve2_list=$modorder->field('id,uid,fuid,guid,income,leve1,leve2,leve3')->where($where_leve2)->order('id desc')->select();


foreach($leve2_list as $k2=>$v2){
$leve2_list[$k2]['income']=Orebate2(array('price'=>$v2['income'],'leve'=>$v2['leve2']));
$leve2_stat=$leve2_stat+$leve2_list[$k2]['income']; //1
}


$where_leve3['_complex'] = $map;
$where_leve3['guid']=$uid;

$leve3_list=$modorder->field('id,uid,fuid,guid,income,leve1,leve2,leve3')->where($where_leve3)->order('id desc')->select();


foreach($leve3_list as $k3=>$v3){
$leve3_list[$k3]['income']=Orebate2(array('price'=>$v3['income'],'leve'=>$v3['leve3']));
$leve3_stat=$leve3_stat+$leve3_list[$k3]['income']; //2
}


if($v['webmaster_pid'] && $v['webmaster']==1){
$rmap['up_time'] = array(
            array(
                'egt',
                $start
            ),
            array(
                'elt',
                $end
            )
);	
$rmap['status']=3;
$rmap['nstatus']=2;
$rmap['add_time'] = array('egt',1559318400);
$where_leve4['_complex'] = $rmap;
$where_leve4['relation_id']=$v['webmaster_pid'];
$leve4_list=$modorder->field('id,uid,fuid,guid,income,leve1')->where($where_leve4)->order('id desc')->select();
foreach($leve4_list as $k4=>$v4){
$leve4_list[$k4]['income']= round($v4['income']*($v4['leve1']/100),2);
$leve4_stat=$leve4_stat+$leve4_list[$k4]['income']; //2
}

}

$isql=array(
		'add_time'=>date('Y-m-d',NOW_TIME),
		'type'=>3,
		'uid'=>$uid
);
$isset=$modfin->field('id')->where($isql)->find();
$money=$leve1_stat+$leve2_stat+$leve3_stat+$leve4_stat;
if(!$isset){
$data=array(
'add_time'=>date('Y-m-d',NOW_TIME),
'price'=>$money,
'mark'=>date('Y-m',$start),
'status'=>2,
'backcash'=>0,
'income'=>$money,
'uid'=>$uid,
'type'=>3
);

$result=$modfin->add($data);
	
	if($result && $money>0){
		
		$datacash=array(
		'create_time'=>NOW_TIME,
		'money'=>$money,
		'remark'=>date('Y-m',$start),
		'status'=>1,
		'uid'=>$uid,
		'type'=>3
		);

	$ret=$modusercash->add($datacash);
	if($money>0){
	$moduser->where(array('id'=>$uid))->setInc('money',$money);	
	}	
$money=0;
$msg++;
	
	}

}
// agent end 
}else{
$sql_list=array();
$sql_leve1['_complex'] = $map;
$sql_leve1['uid']=$uid;

$sql_list=$modorder->field('id,uid,fuid,guid,income,leve1,leve2,leve3')->where($sql_leve1)->order('id desc')->select();

$user_stat=0;
	foreach($sql_list as $ko=>$vo){
	$sql_list[$ko]['income']=Orebate1(array('price'=>$vo['income'],'leve'=>$vo['leve1']));
	$user_stat=$user_stat+$sql_list[$ko]['income']; //myself
	}

	$usql=array(
			'remark'=>date('Y-m',$start),
			'uid'=>$uid,
			'type'=>3
	);

		$userset=$modusercash->field('id')->where($usql)->find();
		
		if(!$userset && $user_stat>0){
									$datacash=array(
									'create_time'=>NOW_TIME,
									'money'=>$user_stat,
									'remark'=>date('Y-m',$start),
									'status'=>1,
									'uid'=>$uid,
									'type'=>3
									);
									
										        $ret=$modusercash->add($datacash);
										        
												if($ret && $user_stat>0){
												$moduser->where(array('id'=>$uid))->setInc('money',$user_stat);
												
												$msg++;
												
												}
												
										
		}



}







}
	
}

        $allpage=ceil($count/$page_size);
        $startpage==1?0:$startpage;
        $result_data['page']	 = $startpage==$allpage?0:$startpage+1;
        $result_data['msg']	 = $msg;
        $result_data['coll']		= $startpage;
        $result_data['totalcoll']	= $totalcoll;
        $result_data['totalnum']	=   $count;
        $result_data['thiscount']	= count($rows);
        $result_data['times']		= time();
        
        
        S('tuiquanke_collect_jiesuan', array(
            'page'=>$startpage==$allpage?0:$startpage+1,
            'totalcoll'=>$totalcoll,
            'allpage'=>$allpage,
            'msg'=>$msg,
            'coll'=>$startpage,
        ),86400);
        
        return $result_data;


	
}
	

public function tuiquanke()
{
$Now=NOW_TIME;
if(date('d',$Now)==trim(C('yh_ComputingTime'))){
$moduser=M('user');
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/Runtime/Data/tuiquanke_collect_jiesuan.php';
$ret=opcache_invalidate($dir,TRUE);
}
 $tuiquanke_collect = S('tuiquanke_collect_jiesuan');
if(!$tuiquanke_collect || $tuiquanke_collect===false){
$page = 1;
$allpage=2;
}else{
$page =$tuiquanke_collect['page'];
$allpage=$tuiquanke_collect['allpage'];
 }

$page_size=15;

if($page==0){
$page=1;
}

$result_data = $this->tuiquanke_collect($page,$page_size);


if($result_data){
	    $this->assign('result_data', $result_data);
	    $resp = $this->fetch('collect');
	    $this->ajaxReturn($page==$allpage?0:1, $page==$allpage?'结算数据已经生成完成！':$page+1, $resp);
}
	
	
}else{

$page=1;	
$resp = $this->fetch('collect');
$this->ajaxReturn($page==1?0:1, $page==1?'请每月'.trim(C('yh_ComputingTime')).'号再来生成！':$page+1, $resp);	
	


}





}



	
public function flist(){
	
$p = I('p', 1, 'intval');
$page_size = 20;
$start = $page_size * ($p - 1);
$mod=M($this->_name);
if($_GET['status']){
$where['status']=I('status', 'trim');
 } 

 
 $where['type']=array(array('eq',1),array('eq',3),'or');

 if($_GET['keyword']){
 	
     $where['uid'] = M('user')->where('phone ='.I('keyword','','trim'))->getfield('id');
	 
 }
 
 if($_GET['time_start']){
	
$where['add_time']=I('time_start','','trim');
	
 } 
 

 
$prefix = C(DB_PREFIX);
$field = '*,
   (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'finance.uid limit 1) as phone,
   (select name from '.$prefix.'apply where '.$prefix.'apply.uid = '.$prefix.'finance.uid limit 1) as name,
   (select alipay from '.$prefix.'apply where '.$prefix.'apply.uid = '.$prefix.'finance.uid limit 1) as alipay';
$rows = $mod->field($field)->where($where)->order('id desc')->limit($start . ',' . $page_size)->select();
$count = $mod->where($where)->count();
$pager = new \Think\Page($count, $page_size);
$this->assign('page', $pager->show());
$this->assign('total_item', $count);
$this -> assign('page_size',$page_size);
foreach($rows as $k=>$v){
		$list[$k]['status']=$this->orderstatic($v['status']);
		$list[$k]['status_t']=$v['status'];
		$list[$k]['price']='￥'.$v['price'];
		$list[$k]['mark']=$v['mark'];
		$list[$k]['name']=$v['name'];
		$list[$k]['alipay']=$v['alipay'];
		$list[$k]['income']='￥'.$v['income'];
		$list[$k]['add_time']=$v['add_time'];
		$list[$k]['phone']=$v['phone'];
		$list[$k]['uid']=$v['uid'];
		$list[$k]['type']=$v['type'];
		$list[$k]['id']=$v['id'];
		$list[$k]['backcash']='￥'.$v['backcash'];
		
}

$this->assign('orderlist',$list);

$this->display();
		
		
}


public function edit_status(){
	
$id = I('id','','trim');

if(!empty($id)){
$where=array(
'id'=>$id
);
$data=array(
'status'=>1
);
$res=M('finance')->where($where)->save($data);
if($res){
IS_AJAX && $this->ajaxReturn(1, L('operation_success'));
                $this->success(L('operation_success'));	

}else{
	
 IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
}

	
}



    
	
	
}




public function delete_f()
    {
        $mod = D($this->_name);
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



protected function orderstatic($id){
switch($id){
	case 2 :
	return '待付款';
	break;
	case 1 :
	return '已付款';
	
	default : 
	return '结算异常';
	break;
}
	
}

	
	
	
}


