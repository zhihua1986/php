<?php
namespace Common\Model;
use Think\Model;
class brandModel extends Model
{


protected $fields = array('id','cate_id','logo','brand','recommend','status','ordid','add_time','remark');
protected $pk     = 'id';
	

public function BrandList($size,$order,$where=array()){
$cate_list = array();
$where['status'] = 1;
$where['id'] = array('gt',0);
$cate_list = $this->field('id,brand,remark,logo,cate_id')->where($where)->order($order)->limit($size)->select();

 return $cate_list;
	
}



}