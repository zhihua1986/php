<?php
namespace Common\Model;
use Think\Model;
class MtorderModel extends Model
{
 protected $pk     = 'id';	
 protected $updateFields = array('status','settle_time');
 protected $_validate = array(
     array('orderid','','已经存在！',0,'unique',1),
   );

	public function setError($str=null){
			$this->error=$str;
	}

}