<?php
namespace Home\Action;
class BrandAction extends BaseAction
{
	public function _initialize() {
        parent::_initialize();
        $this->_mod = D('brandcate')->cache(true, 10 * 60);
    }
public function index(){
    $brand_cate=$this->_mod->field('id,name')->where('status=1')->order('ordid asc')->select();
   if($brand_cate){
   	$field='id,logo,brand,cate_id';
    $sql='';
    $si=0;
	$k=0;
	foreach($brand_cate as $k=>$v){
	if($si==0){
         $sql='(SELECT '.$field.' from '.C("DB_PREFIX").'brand where cate_id='. $v['id'] .' and status=1 order by ordid asc)';
        }else{
            $sql=$sql.' union all (SELECT '.$field.' from '.C("DB_PREFIX").'brand where cate_id='. $v['id'] .' and status=1 order by ordid asc)';		
        }
        $si++;	
		
       $cate[$v['id']]['id']=$v['id'];
       $cate[$v['id']]['name']=$v['name'];
		
	}
	$Model = M();
    $list=$Model->cache(true, 10 * 60)->query($sql);
	 $cateid=0;
    $pi=0;
    foreach($list as $p){
        $newsale[$p['cate_id']][$pi]['logo']=$p['logo'];
        $newsale[$p['cate_id']][$pi]['brand']=$p['brand'];
        $newsale	[$p['cate_id']][$pi]['id']=$p['id'];
        $pi++;
    }
    foreach($brand_cate as $i=>$c){
        $product[$k]['brandlist']=$newsale[$c['id']];
        $product[$k]['name']=$cate[$c['id']]['name'];
        $product[$k]['cid']=$cate[$c['id']]['id'];
        $k++;
    }
	
	 $this->assign('brand', $product);
	 

   	
   }

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