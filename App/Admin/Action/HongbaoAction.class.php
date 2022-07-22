<?php
namespace Admin\Action;
use Common\Model;
class HongbaoAction extends BaseAction
{
	
public function _initialize()
    {
     parent::_initialize();
     $this->_name = 'hongbao';
	 $this->mod=M('hongbao');
  }
	
	
protected function randnum($money,$number,$ratio= 0.5){
        $res = array(); 
        $min = ($money / $number) * (1 - $ratio); 
        $max = ($money / $number) * (1 + $ratio);  
        for($i=0;$i<$number;$i++){
            $res[$i] = $min;
        }
        $money = $money - $min * $number;
        $randRatio = 100;
        $randMax = ($max - $min) * $randRatio;
        for($i=0;$i<$number;$i++){
            $randRes = mt_rand(0,$randMax);
            $randRes = $randRes / $randRatio;
            if($money >= $randRes){ 
                $res[$i]    += $randRes;
                $money      -= $randRes;
            }
            elseif($money > 0){    
                $res[$i]    += $money;
                $money      -= $money;
            }
            else{                 
                break;
            }
        }
        if($money > 0){
            $avg = $money / $number;
            for($i=0;$i<$number;$i++){
                $res[$i] += $avg;
            }
            $money = 0;
        }
        shuffle($res);
        foreach($res as $k=>$v){
            preg_match('/^\d+(\.\d{1,2})?/',$v,$match);
            $match[0]   = number_format($match[0],2);
            $res[$k]    = $match[0];
        }

        return $res;
    }


public function detail(){
	
$id=$this->_request('id');
$list=M('hongbao_detail')->where('hid='.$id)->select();
$this->assign('list',$list);
	
	
if (IS_AJAX) {
                $response = $this->fetch();
                $this->ajaxReturn(1, '', $response);
            } else {
                $this->display();
    }
}


 public function deleted()
    {
        $mod = D($this->_name);
		$child_mod=M('hongbao_detail');
        $pk = $mod->getPk();
		
        $ids = trim($this->_request($pk), ',');
        if ($ids) {
            if (false !== $mod->delete($ids)) {
            	
				$res=$child_mod->where('hid in('.$ids.')')->delete();
				
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'));
                $this->success(L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
        } else {
            IS_AJAX && $this->ajaxReturn(0, L('illegal_parameters'));
            $this->error(L('illegal_parameters'));
        }
    }



public function added(){
	
       if (IS_POST) {
        	$mod=$this->mod;
            if (false === $data = $mod->create()) {
                IS_AJAX && $this->ajaxReturn(0, $mod->getError());
                $this->error($mod->getError());
            }
            if (method_exists($this, '_before_insert')) {
                $data = $this->_before_insert($data);
            }

			  $data['add_time'] = time();
			  $data['push_time'] =strtotime($data['push_time']);
			  $data['status'] = 0;
		
            if( $mod->add($data) ){
            	
             if( method_exists($this, '_after_insert')){
                    $id = $mod->getLastInsID();
                    $this->_after_insert($id);
             }
	 $hid=$mod->getLastInsID();
			
	  $child=$this->randnum($data['price'],$data['num']);	
	  foreach($child as $k=>$v){
	   $child_data=array(
			 'hid'=>$hid,
			 'price'=>$v,
			 'add_time'=>time(),
			 'status'=>0,
		);
		$res=M('hongbao_detail')->add($child_data);
		}	
			
               IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'add');
                $this->success(L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
	
	}	
	
}

	
		
}
	