<?php
namespace Common\Model;
use Think\Model;
class orderModel extends Model
{


public function ajax_yh_update_order($item) {
	$prefix = C(DB_PREFIX);
	$table=$prefix.'order';
	$sql='select id from '.$table.' where status=0 and orderid ="'.$item['orderid'].'" and goods_iid="'.$item['goods_iid'].'" and format('.$table.'.price,2) = format('.$item['price'].',2)';
	$res=M()->query($sql);

 if ($item['status']=='订单结算' && $res){
 	
	$map=array(
	'id'=>$res['id']
	);
    	
	$data=array(
	'up_time'=>$item['up_time'],
	'status' =>3
	);
	
	$result=M('order')->where($map)->save($data);
	
	if($result){
	return 1;
	}else{
	return 0;
	}
		
		
	}else{
			
		return 0;

        }
     
    }



public function ajax_yh_publish_stat($item){

	 $prefix = C(DB_PREFIX);
	 $table=$prefix.'order';
	$sql='select id from '.$table.' where status=0 and orderid ="'.$item['orderid'].'" and goods_iid="'.$item['goods_iid'].'" and format('.$table.'.price,2) = format('.$item['price'].',2)';
	$num=M()->execute($sql);

    if ($num>=1){
         return 0;
		}else{
		$this->create($item);
        $item_id = $this->add();
		
        if ($item_id) {
            return 1;
        } else {
            return 0;
        }
        

        }
     
    }
    
    

}