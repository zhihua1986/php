<?php
namespace Admin\Action;
use Common\Model;
use Common\Model\userModel;
vendor('PHPExcel.PHPExcel');
class ThirdorderAction extends BaseAction
{
  //  protected $list_relation = true;
    
    public function _initialize()
    {
     parent::_initialize();
     $this->tqkuid=trim(C('yh_app_kehuduan'));
	$tkapi=trim(C('yh_api_line'));
	if(false ===$tkapi){
		$this->tqkapi = 'http://api.tuiquanke.cn';
		}else{
		$this->tqkapi = $tkapi;
	}
        $this->assign('list_table', true);
        $this->_name = 'dmorder';
    }

	
public function sync(){
F('dmjiesuan',null);
 if (IS_AJAX){
                $response = $this->fetch();
                $this->ajaxReturn(1, '', $response);
    } else {
                $this->display('sync');
    }
}	


public function sync_order()
{
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/Runtime/Data/dmjiesuan.php';
$ret=opcache_invalidate($dir,TRUE);
}
 $tuiquanke_collect = F('dmjiesuan');
 $time=time();
 $starttime=I('starttime');
 $pagesize=ceil(($time-$starttime)/3600);
if(!$tuiquanke_collect || $tuiquanke_collect===false){
$page = 1;
}else{
$page =$tuiquanke_collect['page'];
 }
$result_data = $this->sync_scene($page,$starttime,$pagesize);
	    if($result_data){
	    $this->assign('result_data', $result_data);
	    $resp = $this->fetch('collect_api');
	    $this->ajaxReturn($page==$pagesize?0:1, $page==$pagesize?'同步完成或者没有新数据了':$page+1, $resp);
	    }
}	
	
protected function sync_scene($page,$starttime,$pagesize){
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/Runtime/Data/dmjiesuan.php';
$ret=opcache_invalidate($dir,TRUE);
}
$tuiquanke_collect = F('dmjiesuan');
if(!$tuiquanke_collect || $page==1){
            $coll=0;
            $totalcoll = 0;
}else{
           $coll = $tuiquanke_collect['coll'];
           $totalcoll = $tuiquanke_collect['totalcoll'];
}	
 
$starttime=$starttime+($page*3600);
$endtime=$starttime+3600;
$json = $this->GetDmOrder($starttime,$endtime);
if($json['data']){
         $raw = array();
   		 $n=0;
         foreach ($json['data'] as $key => $query){
          if($query['order_sn'] && $query['site_id'] == trim(C('yh_dm_pid'))){
                $raw = array(
                    'ads_id'=>$query['ads_id'],
                    'goods_id'=>$query['details'][0]['goods_id'],
                    'orders_price'=>$query['orders_price'],
                    'goods_name'=>$query['details'][0]['goods_name'],
                   //  'goods_img'=>$query['details'][0]['goods_img'],
					'order_status'=>$query['details'][0]['order_status'],
					'order_commission'=>$query['details'][0]['order_commission'],
					'order_sn'=>$query['details'][0]['order_sn'],
					'order_time'=>strtotime($query['order_time']),
					'uid'=>$query['euid'],
					'status'=>$query['status'],
					'confirm_price'=>$query['confirm_price'],
					'confirm_siter_commission'=>$query['confirm_siter_commission'],
					'charge_time'=>$query['charge_time']!='0000-00-00 00:00:00'?strtotime($query['charge_time']):'',
					'ads_name'=>$query['ads_name'],
                    'leve1'=> trim(C('yh_bili1')),
                    'leve2'=> trim(C('yh_bili2')),
                    'leve3'=> trim(C('yh_bili3')),
                );
                if($this->_ajax_duomai_order($raw)){
			    $n++;
			    }
               
            }   
              
            }	
 	
	
}else{
	$n = 0;
}


        $result_data['p']	 = $page;
        $result_data['msg']	 = $msg;
        $result_data['coll']		= $n;
        $result_data['totalcoll']	= $pagesize;
        $result_data['totalnum']	=   $n;
        $result_data['thiscount']	= count($json);
        $result_data['times']		= time();
        F('dmjiesuan', array(
            'coll'=>$coll, 
            'page'=>$page==$pagesize?0:$page+1,
            'totalcoll'=>$pagesize
        ));
        
        return $result_data;
	
}




public function index(){
$this->tabname='dmorder';
$p = I('p', 1, 'intval');
$page_size = 20;
$start = $page_size * ($p - 1);
$mod=M($this->tabname);
if(I('status')){
$where['status']=I('status');
 } 

 if($_GET['keyword']){
     $where['order_sn'] = I('keyword','','trim');
 }
 
 if($_GET['smstitle']){
     $where['goods_name'] = array('like','%'.I('smstitle').'%');
 }
 
if($_GET['id']){
     $where['uid'] = I('id');
 }
 
$prefix = C(DB_PREFIX);
$field = '*,
   (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'dmorder.uid limit 1) as nickname';
$rows = $mod->field($field)->where($where)->order('id desc')->limit($start . ',' . $page_size)->select();
$count = $mod->where($where)->count();
$pager = new \Think\Page($count, $page_size);
$this->assign('page', $pager->show());
$this->assign('total_item', $count);
$this -> assign('page_size',$page_size);
foreach($rows as $k=>$v){
		$list[$k]['settle_time']=$v['charge_time'];
		$list[$k]['id']=$v['id'];
		$list[$k]['smstitle']=$v['goods_name'];
		$list[$k]['nickname']=$v['nickname'];
		$list[$k]['orderid']=$v['order_sn'];
		$list[$k]['paytime']=$v['order_time'];
		$list[$k]['payprice']=$v['orders_price'];
		$list[$k]['profit']=$v['order_commission'];
		$list[$k]['sid']=$v['ads_name'];
		$list[$k]['uid']=$v['uid'];
		$list[$k]['fuid']=$v['fuid'];
		$list[$k]['guid']=$v['guid'];
		$list[$k]['leve1']=$v['leve1'];
		$list[$k]['leve2']=$v['leve2'];
		$list[$k]['leve3']=$v['leve3'];
		$list[$k]['settle']=$v['settle'];
		$list[$k]['order_status']=$v['order_status'];
		$list[$k]['status']=$this->DuomaiStatus($v['status']);
}

$this->assign('orderlist',$list);
$this->display();
}


public function editorder(){
$id = $this->params['id'];
$mod  = M('dmorder');
$info = 	$mod ->where(array('id'=>$id))->find();
if(IS_POST){
if($this->params['uid']){
$userinfo = M('user')->field('webmaster_rate,fuid,guid')->where(array('id'=>$this->params['uid']))->find();
if($userinfo){ 
	$data['leve1']=$userinfo['webmaster_rate']?$userinfo['webmaster_rate']:trim(C('yh_bili1'));
	$data['fuid']=$userinfo['fuid'];
	$data['guid']=$userinfo['guid'];
	}
}
$data['uid'] = trim($this->params['uid']);
$res = $mod->where(array('id'=>$id))->save($data);
if($res !== false){
return $this->ajaxReturn(1,'修改成功！');	
}else{
return $this->ajaxReturn(0,'修改失败！');
	
}
	
}

$this->assign('info',$info);
   $response = $this->fetch();
   $this->ajaxReturn(1, '', $response);
	
}

	
public function pdd_delete_f()
    {
        $mod = D('dmorder');
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





}