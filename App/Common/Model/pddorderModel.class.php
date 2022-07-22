<?php
namespace Common\Model;
use Think\Model;
class pddorderModel extends Model{
	
protected $fields = array('id','order_sn','order_amount','promotion_amount','order_status','p_id','order_pay_time'
,'order_settle_time','order_verify_time','uid','goods_id','fuid','guid','leve1','leve2','leve3','goods_name','status'
,'settle');
protected $pk   = 'id';
	
	
}