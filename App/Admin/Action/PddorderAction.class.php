<?php
namespace Admin\Action;
use Common\Model;
use Common\Model\userModel;
vendor('PHPExcel.PHPExcel');
class PddorderAction extends BaseAction
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
        $this->_name = 'pddorder';
    }

	
public function sync(){
F('pddjiesuan',null);
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
$dir=$basedir.'/data/Runtime/Data/pddjiesuan.php';
$ret=opcache_invalidate($dir,TRUE);
}
 $tuiquanke_collect = F('pddjiesuan');
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
$dir=$basedir.'/data/Runtime/Data/pddjiesuan.php';
$ret=opcache_invalidate($dir,TRUE);
}
$tuiquanke_collect = F('pddjiesuan');
if(!$tuiquanke_collect || $page==1){
            $coll=0;
            $totalcoll = 0;
}else{
           $coll = $tuiquanke_collect['coll'];
           $totalcoll = $tuiquanke_collect['totalcoll'];
}	
 
$starttime=$starttime+($page*3600);
$endtime=$starttime+3600;
$map=array(
		'start'=>$starttime,
		'pagesize'=>100,
		'page'=>1,
		'time'=>NOW_TIME,
		'endtime'=>$endtime,
		'tqk_uid'=>$this->tqkuid
		);
$token=$this->create_token(trim(C('yh_gongju')),$map);
$map['token']=$token;
$url = $this->tqkapi.'/pddgetorder';
$content = $this->_curl($url,$map);
$json = json_decode($content, true);
$json = $json['result']['order_list_get_response']['order_list'];
$count=count($json);
if($count>0){
  		 $n=0;
         foreach ($json as $key => $val){
          if($val['order_status']>=0 && $val['p_id'] == trim(C('yh_youhun_secret'))){
                $raw = array(
                    'goods_id'=>$val['goods_id'],
                    'order_sn'=>$val['order_sn'],
                    'order_status'=>$val['order_status_desc'],
                    'order_amount'=>$val['order_amount']/100,
                    'promotion_amount'=>$val['promotion_amount']/100,
                    'p_id'=>$val['p_id'],
                    'order_pay_time'=>$val['order_pay_time'],
                    'order_settle_time'=>$val['order_settle_time'],
					'order_verify_time'=>$val['order_verify_time'],
                    'goods_name'=>$val['goods_name'],
                    'status'=>$val['order_status'],
                    'uid'=>$val['custom_parameters'],
                    'leve1'=> trim(C('yh_bili1')),
                    'leve2'=> trim(C('yh_bili2')),
                    'leve3'=> trim(C('yh_bili3')),
                );
                if($this->_ajax_pdd_order_insert($raw)){
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
        F('pddjiesuan', array(
            'coll'=>$coll, 
            'page'=>$page==$pagesize?0:$page+1,
            'totalcoll'=>$pagesize
        ));
        
        return $result_data;
	
}




public function index(){
$this->tabname='pddorder';
$p = I('p', 1, 'intval');
$page_size = 20;
$start = $page_size * ($p - 1);
$mod=M($this->tabname);
if($_GET['status'] && $_GET['status']!=''){
$where['status']=I('status','','trim');
 } 

 if($_GET['keyword']){
     $where['order_sn'] = I('keyword','','trim');
 }
 
 if($_GET['goods_name']){
     $where['goods_name'] = array('like','%'.I('goods_name').'%');
 }
 
if($_GET['id']){
     $where['uid'] = I('id');
 }
 
$prefix = C(DB_PREFIX);
$field = '*,
   (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'pddorder.uid limit 1) as nickname';
$rows = $mod->field($field)->where($where)->order('id desc')->limit($start . ',' . $page_size)->select();
$count = $mod->where($where)->count();
$pager = new \Think\Page($count, $page_size);
$this->assign('page', $pager->show());
$this->assign('total_item', $count);
$this -> assign('page_size',$page_size);
foreach($rows as $k=>$v){
		$list[$k]['goods_name']=$v['goods_name'];
		$list[$k]['order_sn']=$v['order_sn'];
		$list[$k]['id']=$v['id'];
		$list[$k]['uid']=$v['uid'];
		$list[$k]['leve1']=$v['leve1'];
		$list[$k]['order_amount']=$v['order_amount'];
		$list[$k]['promotion_amount']=$v['promotion_amount'];
		$list[$k]['order_pay_time']=$v['order_pay_time'];
		if($v['order_settle_time']){
		$list[$k]['order_settle_time']=$v['order_settle_time'];	
		}
		$list[$k]['order_status']=$this->pddstatus($v['status']);
	    $list[$k]['p_id']=$v['p_id'];
		$list[$k]['goods_id']=$v['goods_id'];
		$list[$k]['settle']=$v['settle'];
		if($v['nickname']){
			$list[$k]['nickname']=$v['nickname'];
		}
}

$this->assign('orderlist',$list);

	
$this->display();
}


public function editorder(){
$id = $this->params['id'];
$mod  = M('pddorder');
$info = 	$mod ->where(array('id'=>$id))->find();
if(IS_POST){
$status = $this->params['status'];
if($status == 5 && $this->params['up_time']<1){ 
$data['order_settle_time'] = NOW_TIME;
}
if($this->params['uid']){
$userinfo = M('user')->field('webmaster_rate,fuid,guid')->where(array('id'=>$this->params['uid']))->find();
if($userinfo){ 
	$data['leve1']=$userinfo['webmaster_rate']?$userinfo['webmaster_rate']:trim(C('yh_bili1'));
	$data['fuid']=$userinfo['fuid'];
	$data['guid']=$userinfo['guid'];
	}
}
$data['status'] = $status;
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
        $mod = D('pddorder');
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


public function pddexport(){
$start_time=I('time_start');
$end_time=I('time_end');
if(empty($start_time) || empty($end_time)){
exit('没有选择导出时间段');
}
$status=I('status');
if($status == 5){
$filed_time='order_settle_time';	
}else{
$filed_time='order_pay_time';
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
if($status!='' && $status){
$where['status']= $status;
}
$prefix = C(DB_PREFIX);
$field='*,
(select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'pddorder.uid limit 1) as nickname';
$result=M('pddorder')->field($field)->where($where)->select();
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
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '商品名');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '付款时间');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '付款');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '结算时间');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '商品链接');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '推广位');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', '返利比例');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', '预估收入');
							   
 foreach($result as $k => $v){
     $num=$k+2;
	 if($v['order_settle_time']){
				$up_time=date('Y-m-d H:i:s',$v['order_settle_time']);
				}else{
				$up_time='--';
		}
		
	$income=round($v['promotion_amount']*($v['rates']/100),2);
      $objPHPExcel->setActiveSheetIndex(0)
                          ->setCellValue('A'.$num, ' '.$v['order_sn']) 
                          ->setCellValue('B'.$num, ' '.$v['goods_name'])       
                          ->setCellValue('C'.$num, date('Y-m-d H:i:s',$v['order_pay_time']))
                          ->setCellValue('D'.$num, $v['order_amount'])
						  ->setCellValue('E'.$num, $up_time)
						  ->setCellValue('F'.$num, 'http://mobile.yangkeduo.com/goods2.html?goods_id='.$v['goods_id'])
					      ->setCellValue('G'.$num, $v['p_id'])
						  ->setCellValue('H'.$num, $v['leve1'])
						  ->setCellValue('I'.$num, $v['promotion_amount']);
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




}