<?php
namespace Common\Model;
use Think\Model;
class DmorderModel extends Model
{
 protected $pk     = 'id';	
 protected $updateFields = array('status','orders_price','order_status','order_commission','charge_time','goods_name');
 protected $_validate = array(
     array('order_sn','','已经存在！',0,'unique',1),
   );

	public function setError($str=null){
			$this->error=$str;
	}

}