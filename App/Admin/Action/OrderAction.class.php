<?php
namespace Admin\Action;
use Common\Model;
use Common\Model\userModel;
vendor('PHPExcel.PHPExcel');
vendor('parsecsv.parsecsv');
class OrderAction extends BaseAction
{
  //  protected $list_relation = true;
    
    public function _initialize()
    {
     parent::_initialize();
     $this->tqkuid=trim(C('yh_app_kehuduan'));
	$tkapi=trim(C('yh_api_line'));
	if(false ===$tkapi){
		$this->tqkapi = 'http://api.tuiquanke.com';
		}else{
		$this->tqkapi = $tkapi;
	}
        $this->assign('list_table', true);
        $this->_name = 'order';
    }

public function editorder(){
$id = $this->params['id'];
$mod  = M('order');
$info = 	$mod ->where(array('id'=>$id))->find();
if(IS_POST){
$status = $this->params['status'];
if($status == 3 && $this->params['up_time']<1){ 
$data['up_time'] = NOW_TIME;
}

if($this->params['uid']){
$userinfo = M('user')->field('oid,fuid,guid')->where(array('id'=>$this->params['uid']))->find();
if($userinfo){ 
	$data['oid']=$userinfo['oid'];
	$data['fuid']=$userinfo['fuid'];
	$data['guid']=$userinfo['guid'];
	}
}

$data['status'] = $status;
$data['uid'] = trim($this->params['uid']);
$data['relation_id'] = $this->params['relation_id'];

$res = $mod->where(array('id'=>$id))->save($data);

if($res){
return $this->ajaxReturn(1,'修改成功！');	
}else{
return $this->ajaxReturn(0,'修改失败！');
	
}
	
}


$this->assign('info',$info);
   $response = $this->fetch();
   $this->ajaxReturn(1, '', $response);
	
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
    
    protected function _search()
    {
        $map = array();
        
        if($_GET['status']){
            $map['status'] = I('status','','trim');
        }
        
        if($_GET['keyword']){
            $map['orderid'] = I('keyword','','trim');
        }
		
        if($_GET['agentid']){
            $map['uid'] = I('agentid','','trim');
        }
        
        if($_GET['relation_id']){
            $map['relation_id'] = I('relation_id','','trim');
        }
		
        return $map;
    }
	
	
	
public function export_payed(){
		
 if (IS_AJAX) {
                $response = $this->fetch();
                $this->ajaxReturn(1, '', $response);
            } else {
                $this->display();
            }
	
}


public function export_sus(){
 if (IS_AJAX) {
                $response = $this->fetch();
                $this->ajaxReturn(1, '', $response);
            } else {
                $this->display();
            }
	
}


public function export_weiquan(){
 if (IS_AJAX) {
                $response = $this->fetch();
                $this->ajaxReturn(1, '', $response);
            } else {
                $this->display();
            }
	
}



	
public function ajax_upload_xls(){
	
        if (!empty($_FILES['img']['name'])) {
		if($_FILES['img']){
	            $file = $this->_upload($_FILES['img'], 'xls');
	            if($file['error']) {
	            	$this->ajaxReturn(0,$file['info']);
	            } else {
	             $data['img']=$file['pic_path'];
				 $this->ajaxReturn(1, L('operation_success'), $data['img']);
	            }
	   		}	
//          $result = $this->_upload($_FILES['img'], 'xls/');
//          if ($result['error']) {
//              $this->error(0, $result['info']);
//          } else {
//              $data['img'] = $result['info'][0]['savename'];
//              $this->ajaxReturn(1, L('operation_success'), "./".C( "yh_attach_path" ).'xls/'.$data['img']);
//          }
			
			
        } else {
            $this->ajaxReturn(0, L('illegal_parameters'));
        }
    }
	
	
	
	
public function export(){
	
$start_time=I('time_start');
$end_time=I('time_end');

if(empty($start_time) || empty($end_time)){
	
exit('Export time must be chosen');
	
}

$webmaster=I('agentid');
$status=I('status');

if($status == 3){
	
$filed_time='up_time';	
	
}else{
	
$filed_time='add_time';
}

$where[$filed_time] = array(
            array(
                'egt',
                strtotime($start_time)
            ),
            array(
                'elt',
                strtotime($end_time)
            )
);

if($status){

$where['status']= $status;
	
}

if($webmaster){

$where['uid']= $webmaster;
	
}
$prefix = C(DB_PREFIX);
$field='*,
(select webmaster_rate from '.$prefix.'user where '.$prefix.'user.webmaster_pid = '.$prefix.'order.ad_id limit 1) as rates';
$result=M('order')->field($field)->where($where)->select();

if($result){
 $objPHPExcel = new \PHPExcel();
	
 $objPHPExcel->getProperties()->setCreator("tuiquanke.com")
                               ->setLastModifiedBy("tuiquanke.com")
                               ->setTitle("订单数据导出")
                               ->setSubject("订单数据导出")
                               ->setDescription("备份数据")
                               ->setKeywords("excel")
                               ->setCategory("result file");
							   
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '订单号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '付款时间');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '付款金额');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '商品名称');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '结算时间');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '商品链接');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '推广位名称');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', '推广位ID');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', '预计佣金');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', '预计分成');
		//$objPHPExcel->getActiveSheet()->setCellValue('K1', '扣除站长积分返款');
							   	
							   
 foreach($result as $k => $v){
              $num=$k+2;
	 if($v['up_time']){
				$up_time=date('Y-m-d H:i:s',$v['up_time']);
				}else{
				$up_time='--';
		}
				
	$cashback=round(($v['integral']*(abs(C('yh_fanxian'))/100))*($v['rates']/100),2);
	
	$rate = $v['rates']>0?$v['rates']:$v['leve1'];
	$income=round($v['income']*($rate/100),2);
	 
              $objPHPExcel->setActiveSheetIndex(0)
                          ->setCellValue('A'.$num, ' '.$v['orderid'])    
                          ->setCellValue('B'.$num, date('Y-m-d H:i:s',$v['add_time']))
                          ->setCellValue('C'.$num, $v['price'])
						  ->setCellValue('D'.$num, $v['goods_title'])
						  ->setCellValue('E'.$num, $up_time)
						  ->setCellValue('F'.$num, 'https://item.taobao.com/item.htm?id='.$v['goods_iid'])
					      ->setCellValue('G'.$num, $v['ad_name'])
						  ->setCellValue('H'.$num, ' '.$v['ad_id'].' ')
						  ->setCellValue('I'.$num, $v['income'])
						  ->setCellValue('J'.$num, $income);
						//  ->setCellValue('K'.$num, $cashback);
            }
 
           $objPHPExcel->setActiveSheetIndex(0);
             header('Content-Type: application/vnd.ms-excel');
             header('Content-Disposition: attachment;filename="'.date('Y-m-d',NOW_TIME).'.xls"');
             header('Cache-Control: max-age=0');
             $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
             $objWriter->save('php://output');
             exit;
		
	
}else{
	
	exit('no data');
	
}



	
	
}
	
	

	
public function index(){
$mod = new userModel();
$webmaster=$mod->field('phone,webmaster_pid,nickname')->where('webmaster = 1')->select();
$this->assign('webmaster',$webmaster);
$p = I('p', 1, 'intval');
$page_size = 20;
$start = $page_size * ($p - 1);
$mod=M($this->_name);

$where=array(
'id'=>array('gt',0),
);

if(I('status')){
$where['status']=I('status','','trim');
 } 
 
 if(I('id')){
$where['uid']=I('id');
 } 

 if(I('keyword')){
     $where['orderid'] = I('keyword');
  }
  
  if(I('goods_name')){
      $where['goods_title'] = array('like','%'.I('goods_name').'%');
   }
  
   if(I('relation_id')){
     $where['relation_id'] = I('relation_id');
  }
  
 
if(I('agentid')){
$where['_string'] ='(fuid = '.trim(I('agentid')).')  OR ( guid = '.trim(I('agentid')).')';
 } 
$prefix = C(DB_PREFIX);
$field = '*,
   (select nickname from '.$prefix.'user where '.$prefix.'user.webmaster_pid = '.$prefix.'order.ad_id limit 1) as webmaster,
   (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'order.uid limit 1) as nickname';
$rows = $mod->field($field)->where($where)->order('add_time desc')->limit($start . ',' . $page_size)->select();
$count = $mod->where($where)->count();
$pager = new \Think\Page($count, $page_size);
$this->assign('page', $pager->show());
$this->assign('total_item', $count);
$this -> assign('page_size',$page_size);
foreach($rows as $k=>$v){
       //$cashback=round(($v['integral']*($this->fanxian/100))*($webmaster_rate/100),2);
	//	$income=round($v['income']*($webmaster_rate/100),2);
		
		$list[$k]['status']=$this->orderstatic($v['status']);
		$list[$k]['orderid']=$v['orderid'];
		$list[$k]['id']=$v['id'];
		$list[$k]['integral']=$v['integral'];
		$list[$k]['income']=$v['income'];
		$list[$k]['add_time']=date('m-d H:i:s',$v['add_time']);
		if($v['up_time']){
		$list[$k]['up_time']=date('m-d H:i:s',$v['up_time']);	
		}
		$list[$k]['goods_iid']=$v['goods_iid'];
	    $list[$k]['goods_title']=$v['goods_title'];
		$list[$k]['ad_name']=$v['ad_name'];
		$list[$k]['price']=$v['price'];
		$list[$k]['settle']=$v['settle'];
		$list[$k]['uid']=$v['uid'];
		$list[$k]['relation_id']=$v['relation_id'];
		$list[$k]['leve1']=$v['leve1'];
		if($v['integral']){
		//$list[$k]['cashback']=$cashback;
		}
		//$list[$k]['payment']=round($income-$cashback,2);
		$list[$k]['nickname']=$v['nickname'];
		if($v['webmaster']){
			$list[$k]['webmaster']='('.$v['webmaster'].')';
		}
		
	
	
	
}




$this->assign('orderlist',$list);

	
$this->display();
	
	
}



protected function orderstatic($id){
switch($id){
	case 0 :
	return '待处理';
	break;
	case 1 :
	return '已付款';
	break;
	case 2 :
	return '无效订单';
	break;
	case 3 :
	return '已结算';
	break;
	default : 
	return '订单失效';
	break;
}
	
}	



	

public function sync_order()
{
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/Runtime/Data/sync_order.php';
$ret=opcache_invalidate($dir,TRUE);
}
 $type=I('type');
 $scene=I('scene');
 $tuiquanke_collect = F('sync_order');
 $time=time();
 $starttime=I('starttime');
 $pagesize=ceil(($time-$starttime)/1200);
if(!$tuiquanke_collect || $tuiquanke_collect===false){
$page = 1;
}else{
$page =$tuiquanke_collect['page'];
 }
 
 if($scene==2){
 $result_data = $this->sync_scene($page,$starttime,$type,$pagesize);	
 }else{
   $result_data = $this->sync_action($page,$starttime,$type,$pagesize);	
 }
	    
	    if($result_data){
	    $this->assign('result_data', $result_data);
	    $resp = $this->fetch('collect_api');
	    $this->ajaxReturn($page==$pagesize?0:1, $page==$pagesize?'同步完成或者没有新数据了':$page+1, $resp);
	    }
	}	
	
protected function sync_scene($page,$starttime,$type,$pagesize){
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/Runtime/Data/sync_order.php';
$ret=opcache_invalidate($dir,TRUE);
}
$tuiquanke_collect = F('sync_order');
if(!$tuiquanke_collect || $page==1){
            $coll=0;
            $totalcoll = 0;
       }else{
           $coll = $tuiquanke_collect['coll'];
           $totalcoll = $tuiquanke_collect['totalcoll'];
 }	
 
$starttime=$starttime+($page*1200);
vendor("taobao.taobao");
$c = new \TopClient();
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$req = new \TbkOrderDetailsGetRequest();
$req->setQueryType("2");
$req->setPageSize("100");
$req->setStartTime(date('Y-m-d H:i:s',$starttime));
$req->setEndTime(date('Y-m-d H:i:s',$starttime+1200));
$req->setOrderScene("2");
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$datares=$resp['data']['results']['publisher_order_dto'];

$apppid=trim(C('yh_taobao_pid'));
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[2];
$val=$datares;
if((3 == $val['tk_status'] || 12 == $val['tk_status']) && $val['alipay_total_price']>0 && $val['site_id'] == $AdzoneId && $datares){
$item = array();
$item_id = explode('-',$val['item_id']);
$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
$count=0;
$item['orderid'] = $val['trade_id'];
$item['add_time'] = strtotime($val['tb_paid_time']);
$item['status'] = 1;
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['item_id'];
$item['item_id'] = $item_id;
$item['goods_title'] = $val['item_title'];
$item['goods_num'] = $val['item_num'];
$item['ad_id'] = $val['adzone_id'];
$item['click_time'] = strtotime($val['click_time']);
$item['income'] = $val['pub_share_pre_fee'];
$item['ad_name'] = $val['adzone_name']?$val['adzone_name']:'渠道订单';
$item['goods_rate'] = $val['total_commission_rate']*100;
$item['oid'] = substr($val['trade_id'],-6,6);
$item['leve1'] = trim(C('yh_bili1'));
$item['leve2'] = trim(C('yh_bili2'));
$item['leve3'] = trim(C('yh_bili3'));
$item['relation_id'] = $val['relation_id'];
$item['special_id'] = $val['special_id'];
if($this->_api_Scene_publish_insert($item)){
	$count++;
}


}elseif($datares){
$item = array();
$count=0;
foreach($datares as $val){
if((3 == $val['tk_status'] || 12 == $val['tk_status']) && $val['site_id'] == $AdzoneId && $val['alipay_total_price']>0){
$item['orderid'] = $val['trade_id'];
$item['add_time'] = strtotime($val['tb_paid_time']);
$item['status'] = 1;
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['item_id'];
$item_id = explode('-',$val['item_id']);
$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
$item['item_id'] = $item_id;
$item['goods_title'] = $val['item_title'];
$item['goods_num'] = $val['item_num'];
$item['ad_id'] = $val['adzone_id'];
$item['click_time'] = strtotime($val['click_time']);
$item['income'] = $val['pub_share_pre_fee'];
$item['ad_name'] = $val['adzone_name']?$val['adzone_name']:'渠道订单';
$item['goods_rate'] = $val['total_commission_rate']*100;
$item['oid'] = substr($val['trade_id'],-6,6);
$item['leve1'] = trim(C('yh_bili1'));
$item['leve2'] = trim(C('yh_bili2'));
$item['leve3'] = trim(C('yh_bili3'));
$item['relation_id'] = $val['relation_id'];
$item['special_id'] = $val['special_id'];

if($this->_api_Scene_publish_insert($item)){
	$count++;
}
}	
}
	
	
}else{
$count=0;	
}
 

        $result_data['p']	 = $page;
        $result_data['msg']	 = $msg;
        $result_data['coll']		= $count;
        $result_data['totalcoll']	= $pagesize;
        $result_data['totalnum']	=   $count;
        $result_data['thiscount']	= count($datares);
        $result_data['times']		= time();
        F('sync_order', array(
            'coll'=>$coll, 
            'page'=>$page==$pagesize?0:$page+1,
            'totalcoll'=>$pagesize
        ));
        
        return $result_data;
 
 
	
}
	
	
	
protected function sync_action($page,$start_time,$type,$pagesize){

if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/Runtime/Data/sync_order.php';
$ret=opcache_invalidate($dir,TRUE);
}
$tuiquanke_collect = F('sync_order');
if(!$tuiquanke_collect || $page==1){
            $coll=0;
            $totalcoll = 0;
       }else{
           $coll = $tuiquanke_collect['coll'];
           $totalcoll = $tuiquanke_collect['totalcoll'];
 }
  
$starttime=$start_time+($page*1200);
vendor("taobao.taobao");
$c = new \TopClient();
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$req = new \TbkOrderDetailsGetRequest();
$req->setPageSize("100");
$req->setStartTime(date('Y-m-d H:i:s',$starttime));
$req->setEndTime(date('Y-m-d H:i:s',$starttime+1200));
$req->setOrderScene("1");
if($type==3){
$req->setQueryType("3");
$req->setTkStatus("3");
}elseif($type==13){
$req->setQueryType("2");		
$req->setTkStatus("13");
}else{
$req->setQueryType("2");	
}
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$datares=$resp['data']['results']['publisher_order_dto'];
$apppid=trim(C('yh_taobao_pid'));
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[2];

$val=$datares;
if($val['alipay_total_price']>0 && $datares){
$item = array();
$count=0;

if($type==3){
$item['orderid'] = $val['trade_id'];
$item['status'] = 3;
$item['income'] = $val['pub_share_fee']; //新增
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['item_id'];
$item['up_time'] = strtotime($val['tk_earning_time']);
if($this->api_yh_publish_update($item)){
	 $count++;
 }
 
}elseif($type==13){
	
$item['status'] = 2;
$item['orderid'] = $val['trade_id'];
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['item_id'];
$item_id = explode('-',$val['item_id']);
$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
$item['item_id'] = $item_id;
if($this->api_yh_publish_fail($item)){
	 $count++;
 }
	
}else{
	
if($AdzoneId == $val['site_id']){
$item['orderid'] = $val['trade_id'];
$item['add_time'] = strtotime($val['tk_paid_time']);
$item['status'] = $val['tk_status'];
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['item_id'];
$item_id = explode('-',$val['item_id']);
$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
$item['item_id'] = $item_id;
$item['goods_title'] = $val['item_title'];
$item['goods_num'] = $val['item_num'];
$item['ad_id'] = $val['adzone_id'];
$item['income'] = $val['pub_share_pre_fee'];
$item['tb_deposit_time'] = $val['tb_deposit_time'];
$item['ad_name'] = $val['adzone_name'];
$item['goods_rate'] = $val['total_commission_rate']*100;
$item['oid'] = substr($val['trade_id'],-6,6);
$item['leve1'] = trim(C('yh_bili1'));
$item['leve2'] = trim(C('yh_bili2'));
$item['leve3'] = trim(C('yh_bili3'));
$item['relation_id'] = $val['relation_id'];
$item['special_id'] = $val['special_id'];
if($this->_api_yh_publish_insert($item)){
	$count++;
}
}

}



}elseif($datares){

$item = array();
$count=0;
if($type == 3){
	
foreach($datares as $val){	
$item['orderid'] = $val['trade_id'];
$item['status'] = 3;
$item['income'] = $val['pub_share_fee']; //新增
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['item_id'];
$item_id = explode('-',$val['item_id']);
$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
$item['item_id'] = $item_id;
$item['up_time'] = strtotime($val['tk_earning_time']);
if($this->api_yh_publish_update($item)){
	 $count++;
 }
 
}

}elseif($type == 13){

foreach($datares as $val){	
$item['orderid'] = $val['trade_id'];
$item['status'] = 2;
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['item_id'];
$item_id = explode('-',$val['item_id']);
$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
$item['item_id'] = $item_id;
if($this->api_yh_publish_fail($item)){
	 $count++;
 }		
 
}
	
	
}else{

foreach($datares as $val){
if($AdzoneId == $val['site_id'] && $val['alipay_total_price']>0){
$item['orderid'] = $val['trade_id'];
$item['add_time'] = strtotime($val['tk_paid_time']);
$item['status'] = $val['tk_status'];
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['item_id'];
$item_id = explode('-',$val['item_id']);
$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
$item['item_id'] = $item_id;
$item['goods_title'] = $val['item_title'];
$item['goods_num'] = $val['item_num'];
$item['ad_id'] = $val['adzone_id'];
$item['income'] = $val['pub_share_pre_fee'];
$item['tb_deposit_time'] = $val['tb_deposit_time'];
$item['ad_name'] = $val['adzone_name'];
$item['goods_rate'] = $val['total_commission_rate']*100;
$item['oid'] = substr($val['trade_id'],-6,6);
$item['leve1'] = trim(C('yh_bili1'));
$item['leve2'] = trim(C('yh_bili2'));
$item['leve3'] = trim(C('yh_bili3'));
$item['relation_id'] = $val['relation_id'];
$item['special_id'] = $val['special_id'];

if($this->_api_yh_publish_insert($item)){
	$count++;
}
}
}	

}


}else{
$count=0;	
}

        $result_data['p']	 = $page;
        $result_data['msg']	 = $msg;
        $result_data['coll']		= $count;
        $result_data['totalcoll']	= $pagesize;
        $result_data['totalnum']	=   $count;
        $result_data['thiscount']	= count($datares);
        $result_data['times']		= time();
        F('sync_order', array(
            'coll'=>$coll, 
            'page'=>$page==$pagesize?0:$page+1,
            'totalcoll'=>$pagesize
        ));
        
        return $result_data;
	
}

	
public function sync(){
F('sync_order',Null);
 if (IS_AJAX) {
                $response = $this->fetch();
                $this->ajaxReturn(1, '', $response);
    } else {
                $this->display('sync');
    }
}
	
	


public function update_weiquan_stat(){ //ok
	
if(IS_POST){
$alixls=I('alixls','','trim');
if(!empty($alixls)){
$alixls=ROOT_PATH.$alixls;
if(is_file($alixls)){
 $file_name=$alixls;
 
		 $csv = new \ParseCsv\Csv();
		 $csv->encoding('UTF-16', 'UTF-8');
		 $csv->delimiter = ",";
		 $csv->parseFile($file_name);
		 $csvdata = $csv->data;
		 
		 $data = array();
		 $i=0;
		$n=0;
		 foreach($csvdata as $k=>$val)
		 {
		 
		 $data['orderid'] = trim($val['淘宝子订单编号']);
		 $data['status'] = trim($val['维权状态']);
		 $data['amount'] = trim($val['结算金额']);
		$data['reamount'] = trim($val['维权退款金额']);
		$data['commission'] = trim($val['应返商家金额']);
		$data['up_time'] = strtotime(trim($val['订单结算时间']));
		 if($this->_ajax_yh_publish_rerund($data)){
		 $n++;
		 }
		
		 $i++;
		 }
 
	 	    $json = array(
                'data'=>array(),
                'total'=>$n,
                'state'=>'yes',
                'msg'=>'导入维权订单成功！'
            );
			
		  @unlink($alixls);
$this->ajaxReturn(1, '导入成功');
		   exit(json_encode($json));
	
}

}

}


}

 private function _ajax_yh_publish_rerund($item)
    {
        $result = $this->ajax_yh_refund_order($item);
		
        return $result;
    }	
	
	
private function ajax_yh_refund_order($item) {
	$prefix = C(DB_PREFIX);
	$table=$prefix.'order';
	$sql='select id,uid,integral,nstatus,income,price from '.$table.' where orderid ="'.$item['orderid'].'"';
	$res=M()->query($sql);
 if ($item['status']=='维权成功' && $res[0]['price']==$item['amount'] && $res && $item['amount']>$item['reamount'] && $item['commission']>0){
 	
 $map=array(
	'id'=>$res[0]['id']
	);
	
  $data=array(
	'up_time'=>$item['up_time'],
	'income' =>$res[0]['income']-$item['commission'],
	'price' =>$res[0]['price']-$item['reamount'],
	);

   $result=M('order')->where($map)->save($data);	
   if($result){return 1;}
 }

 if ($item['status']=='维权成功' && $res[0]['price']==$item['amount'] && $res && $item['amount']==$item['reamount'] && $item['commission']>0){
	$map=array(
	'id'=>$res[0]['id']
	);
    	
	$data=array(
	'up_time'=>$item['up_time'],
	'status' =>2
	);
	$result=M('order')->where($map)->save($data);
if($result){
if($res[0]['integral']>0 && $res[0]['nstatus']==0){
M('user')->where('id='.$res[0]['uid'])->save(array(
 'score'=>array('exp','score-'.$res[0]['integral'])
));
}
	return 1;
	}else{
	return 0;
	}
		
		
	}else{
			
		return 0;

        }
     
    }
	

public function update_pay_stat(){ //ok
	
if(IS_POST){
$alixls=I('alixls','','trim');
if(!empty($alixls)){
$alixls=ROOT_PATH.$alixls;
if(is_file($alixls)){
 $file_name=$alixls;
 $csv = new \ParseCsv\Csv();
 $csv->encoding('UTF-16', 'UTF-8');
 $csv->delimiter = ",";
 $csv->parseFile($file_name);
 $csvdata = $csv->data;
 $data = array();
 $i=0;
 foreach($csvdata as $k=>$val)
 {

 $data[$i]['orderid'] = trim($val['淘宝子订单号']);
 $data[$i]['status'] = trim($val['订单状态']);
 $data[$i]['income'] = trim($val['付款预估收入']);
 $data[$i]['up_time'] = strtotime(trim($val['结算时间']));
 $data[$i]['price'] = number_format(trim($val['付款金额']),2, '.', '');
 $data[$i]['goods_iid'] = trim($val['商品ID']);

 $i++;
 }
				
$result = array();
foreach($data as $val){
    $key = $val['goods_iid'].'_'.$val['price'].'_'.$val['orderid'];
    if(!isset($result[$key])){
        $result[$key] = $val;
    }else{
        $result[$key]['price'] += $val['price'];
    }
}

$n=0;

foreach(array_values($result) as $val){
$item['orderid'] = $val['orderid'];
$item['status'] = $val['status'];
$item['price'] = $val['price'];
$item['income'] = $val['income'];
$item['goods_iid'] = $val['goods_iid'];
$item_id = explode('-',$val['item_id']);
$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
$item['item_id'] = $item_id;
$item['up_time'] = $val['up_time'];
if($this->_ajax_yh_publish_update($item)){
		    $n++;
 }
}
				
			
	 	    $json = array(
                'data'=>array(),
                'total'=>$n,
                'state'=>'yes',
                'msg'=>'同步成功！'
            );
			
		  @unlink($alixls);
$this->ajaxReturn(1, '同步成功');
		   exit(json_encode($json));
	
}

}

}


}



public function putin_pay_stat(){ //ok
if(IS_POST){
$alixls=I('alixls');
if(!empty($alixls)){
$alixls=ROOT_PATH.$alixls;
if(is_file($alixls)){
                $file_name=$alixls;
				$csv = new \ParseCsv\Csv();
				$csv->encoding('UTF-16', 'UTF-8');
				$csv->delimiter = ",";
				$csv->parseFile($file_name);
				$csvdata = $csv->data;
				$data = array();
				$i=0;
				foreach($csvdata as $k=>$val)
				{
			
				$data[$i]['click_time'] = strtotime(trim($val['点击时间']));
				$data[$i]['orderid'] = trim($val['淘宝子订单号']);
				$data[$i]['add_time'] = strtotime(trim($val['创建时间']));
				$data[$i]['status'] = trim($val['订单状态']);
				$data[$i]['price'] = number_format(trim($val['付款金额']),2, '.', '');
				$data[$i]['goods_iid'] = trim($val['商品ID']);
				$data[$i]['goods_title'] = trim($val['商品标题']);
				$data[$i]['goods_num'] = trim($val['商品数量']);
				$data[$i]['ad_id'] = trim($val['推广位ID']);
				$data[$i]['site_id'] = trim($val['媒体ID']);
				$data[$i]['income'] = trim($val['付款预估收入']);
				$data[$i]['ad_name'] = trim($val['媒体名称']);
				$data[$i]['goods_rate'] = trim($val['收入比例']);
				$data[$i]['oid'] = substr(trim($val['淘宝子订单号']),-6,6);
				$data[$i]['relation_id'] = trim($val['渠道关系ID']);
				$data[$i]['deposit_price'] = number_format(trim($val['定金付款金额']),2, '.', '');
				
				$i++;
				}
$result = array();
foreach($data as $val){
    $key = $val['goods_iid'].'_'.$val['price'].'_'.$val['orderid'];
    if(!isset($result[$key])){
        $result[$key] = $val;
    }else{
        $result[$key]['price'] += $val['price'];
        $result[$key]['income'] += $val['income'];
        $result[$key]['ad_name']  = $val['ad_name'].'[合]';
    }
}
$n=0;
$apppid=trim(C('yh_taobao_pid'));
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[2];
foreach(array_values($result) as $val){
if($val['site_id'] == $AdzoneId && $val['price']>0){
$item['orderid'] = $val['orderid'];
$item['add_time'] = $val['add_time'];
$item['status'] = $val['status'];
$item['price'] = $val['price'];
$item['click_time'] = $val['click_time'];
$item['goods_iid'] = $val['goods_iid'];
$item_id = explode('-',$val['goods_iid']);
$item_id = $item_id[1]?$item_id[1]:$val['goods_iid'];
$item['item_id'] = $item_id;
$item['goods_title'] = $val['goods_title'];
$item['goods_num'] = $val['goods_num'];
$item['ad_id'] = $val['ad_id'];
$item['income'] = $val['income'];
$item['ad_name'] = $val['ad_name'];
$item['goods_rate'] = $val['goods_rate'];
$item['oid'] = $val['oid'];
$item['relation_id'] = $val['relation_id'];
$item['deposit_price'] = $val['deposit_price'];
$item['leve1'] = trim(C('yh_bili1'));
$item['leve2'] = trim(C('yh_bili2'));
$item['leve3'] = trim(C('yh_bili3'));
if($this->_ajax_yh_publish_insert($item)){
		    $n++;
}
}
}
			
	 	    $json = array(
                'data'=>array(),
                'total'=>$n,
                'state'=>'yes',
                'msg'=>'成功入库'.$n.'个订单'
       );
			
	    @unlink($alixls);
		$this->ajaxReturn(1, '成功入库'.$n.'个订单');
		exit(json_encode($json));	
	
}
	
}

}
}	
	
	
    
 private function ajax_yh_publish_update($item)
    {
        $result = $this->api_yh_update_order($item);
		
        return $result;
    }


 private function _ajax_yh_publish_insert($item)
    {
        $result =$this->ajax_yh_publish_stat($item);
		
        return $result;
    }
    

	
	
private function ajax_yh_update_order($item) {
	$prefix = C(DB_PREFIX);
	$table=$prefix.'order';
	//$sql='select id,uid,integral,nstatus from '.$table.' where (status=1 or status=2) and orderid ="'.$item['orderid'].'" and goods_iid="'.$item['goods_iid'].'" and format('.$table.'.price,2) = format('.$item['price'].',2)';
	$sql='select id,uid,integral,nstatus from '.$table.' where (status=1 or status=2) and orderid ="'.$item['orderid'].'"';
	$res=M()->query($sql);
 if ($item['status']=='已结算' && $res){
	$map=array(
	'id'=>$res[0]['id']
	);
    	
	$data=array(
	'up_time'=>$item['up_time'],
	'income'=>$item['income'],
	'price'=>$item['price'],
	'status' =>3
	);
$result=M('order')->where($map)->save($data);
if($result){
	
$map=array(
'id'=>$res[0]['uid'],
'tbname'=>array('neq',1),
'webmaster'=>array('neq',1)
);
if($res[0]['integral']>0 && $res[0]['nstatus']==0){
M('user')->where($map)->save(array(
 'score'=>array('exp','score+'.$res[0]['integral'])
));
}
	return 1;
	}else{
	return 0;
	}
		
		
	}else{
			
		return 0;

        }
     
    }

 
    
	
private function ajax_yh_publish_stat($item){

	$prefix = C(DB_PREFIX);
	 $table=$prefix.'order';
	//$sql='select id from '.$table.' where status=1 and orderid ="'.$item['orderid'].'" and goods_iid="'.$item['goods_iid'].'" and format('.$table.'.price,2) = format('.$item['price'].',2)';
	$sql='select id from '.$table.' where orderid ="'.$item['orderid'].'"';
	$num=M()->execute($sql);
	$mod=M('order');
if($num<=0 && ($item['status']=='已付款' || $item['status']=='已结算')){
	
	  $item['status']=1;
	  $item['nstatus']=1;
	  $item['oid']=md5($item['oid']);
		$mod->create($item);
        $item_id = $mod->add();
        if ($item_id) {
            return 1;
        } else {
            return 0;
        }
        
}elseif($item['deposit_price']>0){ //新增

$data['price']=$item['price'];
$data['income']=$item['income'];
$sqlwhere['orderid'] = $item['orderid'];
$res=$mod->where($sqlwhere)->save($data);
if ($res) {
            return 1;
        } else {
            return 0;
        }
	
}else{
	
	return 0;
	
}

     
    }

	
	
	
public function add_score(){
if(IS_POST){
$id=I('id');
$score=I('score','','trim');
$price=I('price','','trim');
if(!empty($id) && !empty($score) && !empty($price)){
$info=M('order')->where('id='.$id)->find();
if($info){
$res=M('order')->where('id='.$id)->save(array(
'status'=>3,
'integral'=>$score,
'price'=>$price,
'up_time'=>time()
));

if($res){
M('user')->where('id='.$info['uid'])->save(array(
 'score'=>array('exp','score+'.$score)
));

return $this->ajaxReturn(1, '操作成功');	
}

	
}

}

return $this->ajaxReturn(0, '操作失败');	

}

if (IS_AJAX) {
            $response = $this->fetch();
            $this->ajaxReturn(1, '', $response);
        } else {
            $this->display();
        }
		
}
	
    
    public function audit()
    {
        $mod = D($this->_name);
        $pk = $mod->getPk();
        
        if(IS_POST){
                
            $id = $this->_post($pk, 'intval');
                
            if (false === $data = $mod->create()) {
                IS_AJAX && $this->ajaxReturn(0, $mod->getError());
                $this->error($mod->getError());
            }
            
            if (false !== $mod->where(array('id'=>$id))->save($data)) {
                
                $item = $mod->find($id);
                
                if($item['status'] == 1){
                    M('user_cash')->add(array(
                        'uid'=>$item['uid'],
                        'money'=>$item['money'],
                        'type'=>1,
                        'remark'=>'充值：'.$item['money'].'元',
                        'data'=>$item['id'],
                        'create_time'=>NOW_TIME,
                        'status'=>1,
                    ));
                    
                    M('user')->where(array('id'=>$item['uid']))->setInc('money', $item['money']);
                }
                
                
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'audit');
                return $this->success(L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                return $this->error(L('operation_failure'));
            }
        }
        
        $id = I($pk,'','intval');
        $info = $mod->find($id);
        $this->assign('info', $info);
        $this->assign('open_validator', true);
        if (IS_AJAX) {
            $response = $this->fetch();
            $this->ajaxReturn(1, '', $response);
        } else {
            $this->display();
        }
    }
    
    private function _ajax_yh_publish_update($item)
    {
        $result = $this->ajax_yh_update_order($item);
		
        return $result;
    }
    
    
    

public function ajax_upload_img() {
if($_FILES['img']){
	            $file = $this->_upload($_FILES['img'], 'charge',$thumb = array('width'=>150,'height'=>150));
	            if($file['error']) {
	            	$this->ajaxReturn(0,$file['info']);
	            } else {
	             $data['img']=$file['mini_pic'];
				 $this->ajaxReturn(1, L('operation_success'), $data['img']);
	            }
	   		 } else {
            $this->ajaxReturn(0, L('illegal_parameters'));
        }
    }
}