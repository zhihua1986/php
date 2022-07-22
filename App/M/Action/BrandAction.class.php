<?php
namespace M\Action;
use Common\Model;
use Think\Page;
class BrandAction extends BaseAction{
	public function _initialize() {
		parent::_initialize();
		$this->_mod = D('brand')->cache(true, 5 * 60);
	}
	public function index(){

		$brand_cate=M('brandcate')->where('status = 1')->field('name,id')->order('ordid asc')->select();
		$this->assign('brand_cate',$brand_cate);
		$page	= I('p',1 ,'intval');
		$size	= 30;
		$cid		= I('cateid','','trim');
		$this->assign('cateid',$cid);
		$start = $size * ($page - 1);
		if($cid){
			$where['cate_id'] = $cid;
		}
		$order = 'ordid asc,id desc';
		$items_list = $this->_mod->where($where)->field('id,logo,brand,remark')->order($order)->limit($start . ',' . $size)->select();	
		$count =$this->_mod->where($where)->count();
		$pager = new Page($count, $size);
		$this->assign('p', $page);
		$this->assign('page', $pager->show());
		$this->assign('total_item', $count);
		$this -> assign('page_size',$size);
		$this->assign('list',$items_list);
if(C('yh_seo_config.brand')){
  $this->_config_seo(C('yh_seo_config.brand'));  	
}else{
$this->_config_seo(array(
            'title'=>'品牌优惠券_'.C('yh_site_name')
)); 
}
		$this->display();


	}

}