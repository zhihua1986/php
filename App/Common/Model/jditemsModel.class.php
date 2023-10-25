<?php
namespace Common\Model;
use Think\Model;
class JditemsModel extends Model
{
protected $fields = array('id','commission_rate','quan','couponlink','pict','itemurl','coupon_price','price','owner'
,'comments','cate_id','itemid','title','item_type','start_time','end_time','cid2','add_time','click_url','shop_name','shop_level','status');
protected $pk     = 'id';	
	
public function Jditems($size,$where,$order){
$where['status'] = 1;
$list=$this->cache(true, 5 * 60)->where($where)->field('id,pict,title,coupon_price,itemid,price,quan,item_type,comments,commission_rate,cate_id')->order($order)->limit($size)->select();
return $list;
}


public function Jdcate($key=''){
$data=array(
'1320'=>'食品饮料',
'16750'=>'个人护理',
'12218'=>'生鲜水果',
'1315'=>'服饰内衣',
'1316'=>'美妆护肤',
'1620'=>'家居日用',
'16750'=>'个人护理',
'737'=>'家用电器',
'15901'=>'家庭清洁/纸品',
'9987'=>'手机通讯',
'6728'=>'汽车用品',
'670'=>'电脑、办公',
'1319'=>'母婴',
);

if($key){
return $data[$key];	
}else{
return $data;
}

}
	

}