<?php
namespace Common\Model;
use Think\Model;
class pdditemsModel extends Model
{


protected $fields = array('id','goods_id','goods_name','goods_desc','goods_thumbnail_url','goods_image_url','sold_quantity','min_group_price','min_normal_price'
,'mall_name','category_id','coupon_discount','coupon_total_quantity','coupon_remain_quantity','coupon_start_time','coupon_end_time','addtime','quanurl','orderid','tuisong','promotion_rate','search_id');
protected $pk     = 'id';


public function GoodsList($size,$where,$order){
$list=$this->cache(true, 5 * 60)->where($where)->field('id,goods_id,goods_name,goods_thumbnail_url,sold_quantity,min_group_price,coupon_discount,min_normal_price,promotion_rate')->order($order)->limit($size)->select();
return $list;
}





}