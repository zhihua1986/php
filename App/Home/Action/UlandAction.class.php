<?php
namespace Home\Action;
class UlandAction extends BaseAction
{
	
	
public function index(){
	$u =urldecode(base64_decode(I('u')));
	$t = urldecode(base64_decode(I('t')));
	$k =urldecode(base64_decode(I('k')));
	if(!$k){
	$k=kouling('https://gd1.alicdn.com/imgextra/i1/126947653/TB2___BalLzQeBjSZFjXXcscpXa-126947653.jpg',$t,$u);
	}
	$data = array(
	'u'=>$u,
	't'=>$t,
	'q'=>I('q'),
	'k'=>$k
	);
	
	$this->assign('data',$data);
	
	$this->display();
	
}
	

}

