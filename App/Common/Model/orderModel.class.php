<?php
namespace Common\Model;
use Think\Model;
class orderModel extends Model{
	
protected $fields = array('id','orderid','uid','add_time','status','price','integral'
,'up_time','goods_iid','goods_title','goods_num','ad_id','ad_name','goods_rate','income','oid','fuid'
,'guid','nstatus','leve1','leve2','leve3','relation_id','special_id','bask','settle','click_time','item_id');
protected $pk     = 'id';
protected $updateFields = array('status','price','up_time','income');
protected $_validate = array(
     array('orderid','','已经存在！',0,'unique',1),
   );

	public function setError($str=null){
			$this->error=$str;
	}
	
	
}