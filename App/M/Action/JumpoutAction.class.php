<?php
namespace M\Action;
use Common\Model;
class JumpoutAction extends BaseAction{
	public function _initialize()
	{
		parent::_initialize();
		$this->_mod = D('pdditems');

	}

public function index(){
if(C('APP_SUB_DOMAIN_DEPLOY')){	
$this->assign('mdomain',str_replace('/index.php/m','',C('yh_headerm_html')));
}else{
$this->assign('mdomain',C('yh_headerm_html'));
}
$id=I('goods_id','0','number_int');
$search_id = I('search_id');
if($id>0){
$where=array(
'goods_id'=>$id
);
$info=$this->_mod->where($where)->getField('quanurl');
if($info){
$quanurl=$info;
}else{
$map=array(
		'goods_id_list'=>$id,
		'multi_group'=>1,
		'search_id'=>$search_id,
		'time'=>time(),
		'tqk_uid'=>$this->tqkuid
);
	    $token=$this->create_token(trim(C('yh_gongju')),$map);
        $map['token']=$token;
        $url = $this->tqkapi.'/pddshortlink';
        $content = $this->_curl($url,$map);
		$json = json_decode($content, true);
	    $quanurl = $json['result'];
		$data=array(
		'quanurl'=>$quanurl
		);
	    $this->_mod->where($where)->save($data);
}

if($quanurl){
echo('<script>window.location.href="'.$quanurl.'"</script>');	
}
}
echo('<script>alert("此商品优惠券失效！");history.go(-1)</script>');	

}




	
}