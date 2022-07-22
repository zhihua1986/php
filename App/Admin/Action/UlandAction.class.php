<?php
namespace Admin\Action;
use Common\Model;
class UlandAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
    }



public function authority(){
	
	$pid = trim(C('yh_youhun_secret'));
	if($pid){
	$where=[
	    'type'=>'pdd.ddk.rp.prom.url.generate',
	    'data_type'=>'JSON',
	    'timestamp'=>$this->msectime(),
	    'client_id'=>trim(C('yh_pddappkey')),
		//'custom_parameters'=>'tqklm',
		'channel_type'=>10,
		'p_id_list'=>'["'.$pid.'"]'
	];
	$where['sign']=$this->create_pdd_sign(trim(C('yh_pddsecretkey')), $where);
	$pdd_api='http://gw-api.pinduoduo.com/api/router';
	$result=$this->_curl($pdd_api, $where, true);
	$list=json_decode($result, true);
	
	if($list['error_response']['sub_msg']){
		exit($list['error_response']['sub_msg']);
	}
	
	if($list['rp_promotion_url_generate_response']['url_list'][0]['url']){
		
		echo('<script> window.location.href="'.$list['rp_promotion_url_generate_response']['url_list'][0]['url'].'"</script>');
		
	}else{
		
		echo '获取授权链接失败';
		
	}
	
	
	}
	
	echo '【站点设置】中拼多多PID没有填写或者没保存！';
	
	
}

 
    public function index()
    {
    	
    if(IS_POST){
    $title = base64_encode(urlencode(I('title')));
    $link = base64_encode(urlencode(I('link')));
    $kouling = base64_encode(urlencode(I('kouling')));
    $quan = base64_encode(urlencode(I('img')));
    $type = I('url_type');
	$mdomain = str_replace(['/index.php/m','/index.php/M'],'',C('yh_headerm_html'));
	$url = $mdomain.'/index.php?m=m&c=uland&a=index&t='.$title.'&u='.$link.'&k='.$kouling.'&q='.$quan;
	return $this->ajaxReturn(1,'修改成功！',$url);
	//echo '复制此链接：'.$url;
    	//exit;
    		
    	}
    	
        $this->display();
    }  
 
}
