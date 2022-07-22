<?php
namespace Home\Action;
vendor('PHPExcel.PHPExcel');
class StatAction extends BaseAction
{

private $accessKey = '';
	
public function _initialize()
    {
        parent::_initialize();
        $this->accessKey = trim(C('yh_gongju'));

    }
	
protected function sotime($st){
switch($st){
	case "最近1天":
	$start =date('Y-m-d',NOW_TIME);
	break;
	case "最近3天":
    $start =date('Y-m-d',strtotime("-3 day"));
	break;
	case "最近7天":
    $start =date('Y-m-d',strtotime("-7 day"));
	break;
	case "最近15天":
    $start =date('Y-m-d',strtotime("-15 day"));
	break;
	case "最近20天":
    $start =date('Y-m-d',strtotime("-20 day"));
	break;
	case "最近30天":
    $start =date('Y-m-d',strtotime("-30 day"));
	break;
	default:
	$start =date('Y-m-d',NOW_TIME);
	break;
}
	
return $start;	

}




public function update_refund_stat(){ //ok
$this->check_key();
$cookie=I('cookie','','trim');
$sotime=I('sotime','','trim');
$local = TQK_DATA_PATH ."3.xls";
if(!empty($cookie)){
if(function_exists('opcache_invalidate')){
$dir=TQK_DATA_PATH.'runtime/Data/coupon/stat_refund.php';
$ret=opcache_invalidate($dir,TRUE);
}
$start = F('coupon/stat_refund');
if(!$start){
$start =date('Y-m-d',strtotime("-2 day"));
}
F('coupon/stat_refund', date('Y-m-d',time()));

if($sotime){
$start=$this->sotime($sotime);
}
$end=date('Y-m-d',time());
$url ='https://pub.alimama.com/report/getTbkRefundOrderDetails.json?downloadId=DOWNLOAD_REPORT_REFUND_ORDER_DETAILS&startTime='.$start.'&endTime='.$end.'&queryType=3&memberType=&isFullRefund=0';
//$url="https://pub.alimama.com/report/getNewTbkRefundPaymentDetails.json?refundType=1&searchType=3&DownloadID=DOWNLOAD_EXPORT_CPSPAYMENT_REFUND_OVERVIEW&startTime=".$start."&endTime=".$end;
set_time_limit(0);
$cp = curl_init($url);
$fp = fopen($local,"w");
curl_setopt($cp, CURLOPT_FILE, $fp);
curl_setopt($cp, CURLOPT_HEADER, 0);
curl_setopt($cp,CURLOPT_FOLLOWLOCATION,1);
curl_setopt($cp, CURLOPT_COOKIE, $cookie);
curl_setopt($cp, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($cp);
curl_close($cp);
fclose($fp);

$filesize=filesize($local);
if(false===$filesize || $filesize<=0){
 @unlink($local);
}

if(is_file($local) && $filesize>0){
                $file_name=$local;
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn(); 
				$n=0;
                for($i=2;$i<=$highestRow;$i++)
                {
                	$data['orderid']= $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
				$data['status']= $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
				$data['up_time']= strtotime($objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue());  
                	//$data['price']= number_format($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue(), 2, '.', '');  
				//$data['goods_iid']= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
			    if($this->_ajax_yh_publish_rerund($data)){
			    $n++;
			    }
			
				}  	
			
	 	    $json = array(
                'data'=>array(),
                'total'=>$n,
                'state'=>'yes',
                'msg'=>'成功同步'.$n.'个淘宝维权订单！'
            );
			
		@unlink($local);
		exit(json_encode($json));
	
}

 $json = array(
                'data'=>array(),
                'total'=>'0',
                'state'=>'no',
                'msg'=>'暂时没有新数据，或者抓取数据异常'
        );

	exit(json_encode($json));

	
}

}



	
public function update_pay_stat(){ //ok
$this->check_key();
$cookie=I('cookie','','trim');
$sotime=I('sotime','','trim');
$local = TQK_DATA_PATH ."2.xls";
if(!empty($cookie)){
if(function_exists('opcache_invalidate')){
$dir=TQK_DATA_PATH.'runtime/Data/coupon/stat_update.php';
$ret=opcache_invalidate($dir,TRUE);
}
$start = F('coupon/stat_update');
if(!$start){
$start =date('Y-m-d',strtotime("-2 day"));
}
F('coupon/stat_update', date('Y-m-d',time()));

if($sotime){
$start=$this->sotime($sotime);
}

$end=date('Y-m-d',time());
$url = 'https://pub.alimama.com/report/getTbkOrderDetails.json?downloadId=DOWNLOAD_REPORT_ORDER_DETAILS&startTime='.$start.'&endTime='.$end.'&queryType=3&tkStatus=3&memberType=';
//$url="https://pub.alimama.com/report/getTbkPaymentDetails.json?queryType=3&payStatus=3&DownloadID=DOWNLOAD_REPORT_INCOME_NEW&startTime=".$start."&endTime=".$end;
set_time_limit(0);
$cp = curl_init($url);
$fp = fopen($local,"w");
curl_setopt($cp, CURLOPT_FILE, $fp);
curl_setopt($cp, CURLOPT_HEADER, 0);
curl_setopt($cp,CURLOPT_FOLLOWLOCATION,1);
curl_setopt($cp, CURLOPT_COOKIE, $cookie);
curl_setopt($cp, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($cp);
curl_close($cp);
fclose($fp);
$filesize=filesize($local);
if(false===$filesize || $filesize<=0){
 @unlink($local);
}

if(is_file($local) && $filesize>0){
                $file_name=$local;
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn(); 
				$n=0;
                for($i=2;$i<=$highestRow;$i++)
                {
                	$data[$i-2]['orderid']= $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
				$data[$i-2]['status']= $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
				$data[$i-2]['up_time']= strtotime($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue());  
                	$data[$i-2]['price']= number_format($objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue(), 2, '.', '');  
				$data[$i-2]['goods_iid']= $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
			
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
$item['goods_iid'] = $val['goods_iid'];
$item['up_time'] = $val['up_time'];
if($this->_ajax_yh_publish_update($item)){
	 $n++;
 }
}	
			
	 	    $json = array(
                'data'=>array(),
                'total'=>$n,
                'state'=>'yes',
                'msg'=>'成功同步'.$n.'个淘宝结算订单！'
            );
			
		@unlink($local);
		exit(json_encode($json));
	
}

 $json = array(
                'data'=>array(),
                'total'=>'0',
                'state'=>'no',
                'msg'=>'暂时没有新数据，或者抓取数据异常'
        );

	exit(json_encode($json));

	
}

}

	
public function putin_pay_stat(){ //ok
$this->check_key();
$cookie=I('cookie','','trim');
$sotime=I('sotime','','trim');
$local = TQK_DATA_PATH ."1.xls";
if(!empty($cookie)){
if(function_exists('opcache_invalidate')){
$dir=TQK_DATA_PATH.'runtime/Data/coupon/stat_start.php';
$ret=opcache_invalidate($dir,TRUE);
}
$start = F('coupon/stat_start');
if(!$start){
$start =date('Y-m-d',strtotime("-2 day"));
}
F('coupon/stat_start', date('Y-m-d',time()));

if($sotime){
$start=$this->sotime($sotime);
}

$end=date('Y-m-d',time());
$url = 'https://pub.alimama.com/report/getTbkOrderDetails.json?downloadId=DOWNLOAD_REPORT_ORDER_DETAILS&startTime='.$start.'&endTime='.$end.'&queryType=2&tkStatus=12&memberType=';
//$url="https://pub.alimama.com/report/getTbkPaymentDetails.json?queryType=1&payStatus=12&DownloadID=DOWNLOAD_REPORT_INCOME_NEW&startTime=".$start."&endTime=".$end;
set_time_limit(0);
$cp = curl_init($url);
$fp = fopen($local,"w");
curl_setopt($cp, CURLOPT_FILE, $fp);
curl_setopt($cp, CURLOPT_HEADER, 0);
curl_setopt($cp, CURLOPT_COOKIE, $cookie);
curl_setopt($cp, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($cp);
curl_close($cp);
fclose($fp);
$filesize=filesize($local);
if(false===$filesize || $filesize<=0){
 @unlink($local);
}


if(is_file($local) && $filesize>0){
	
                $file_name=$local;
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn(); 
				$n=0;
                for($i=2;$i<=$highestRow;$i++)
                {
                	$data[$i-2]['orderid']= $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();  
				$data[$i-2]['add_time']= strtotime($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue());  
				$data[$i-2]['status']= $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
                	$data[$i-2]['price']= number_format($objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue(), 2, '.', '');  
				$data[$i-2]['goods_iid']= $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
				$data[$i-2]['goods_title']= $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
				$data[$i-2]['goods_num']= $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
				$data[$i-2]['ad_id']= $objPHPExcel->getActiveSheet()->getCell("AJ".$i)->getValue();
				$data[$i-2]['site_id']= $objPHPExcel->getActiveSheet()->getCell("AH".$i)->getValue();
				$data[$i-2]['income']= $objPHPExcel->getActiveSheet()->getCell("AD".$i)->getValue();
				$data[$i-2]['ad_name']= $objPHPExcel->getActiveSheet()->getCell("AK".$i)->getValue();
				$data[$i-2]['goods_rate']= $objPHPExcel->getActiveSheet()->getCell("T".$i)->getValue();
				$data[$i-2]['oid']= substr($objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue(),-6,6);
				
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
$item['goods_iid'] = $val['goods_iid'];
$item['goods_title'] = $val['goods_title'];
$item['goods_num'] = $val['goods_num'];
$item['ad_id'] = $val['ad_id'];
$item['income'] = $val['income'];
$item['ad_name'] = $val['ad_name'];
$item['goods_rate'] = $val['goods_rate'];
$item['oid'] = $val['oid'];
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
                'msg'=>'成功入库'.$n.'个淘宝付款订单'
            );
			
		 @unlink($local);
		exit(json_encode($json));
	
}

 $json = array(
                'data'=>array(),
                'total'=>'0',
                'state'=>'no',
                'msg'=>'暂时没有新数据，或者抓取数据异常'
        );

	exit(json_encode($json));

	
}	
}	
	
	

 private function _ajax_yh_publish_update($item)
    {
        $result = $this->ajax_yh_update_order($item);
		
        return $result;
    }
    
 private function _ajax_yh_publish_rerund($item)
    {
        $result = $this->ajax_yh_refund_order($item);
		
        return $result;
    }


 private function _ajax_yh_publish_insert($item)
    {
        $result =$this->ajax_yh_publish_stat($item);
		
        return $result;
    }

private function ajax_yh_refund_order($item) {
	$prefix = C(DB_PREFIX);
	$table=$prefix.'order';
	$sql='select id,uid,integral,nstatus from '.$table.' where orderid ="'.$item['orderid'].'"';
	$res=M()->query($sql);

 if ($item['status']=='维权成功' && $res){
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
	'status' =>3
	);
	$result=M('order')->where($map)->save($data);
	if($result){
if($res[0]['integral']>0 && $res[0]['nstatus']==0){
	
$map=array(
'id'=>$res[0]['uid'],
'tbname'=>array('neq',1),
'webmaster'=>array('neq',1)
);
	
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
	
}else{
	
	return 0;
	
}

     
    }


protected  function check_key(){
 $json = array(
         'state'=>'no',
          'msg'=>'通行密钥不正确'
         );

$key = I('key', '', 'trim');
if(!$key || $key!=$this->accessKey){
exit(json_encode($json));
}	
 }



	
}