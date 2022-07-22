<?php
namespace Admin\Action;
use Common\Model;
class PidAction extends BaseAction{
	
    public function _initialize(){
        parent :: _initialize();
        $this -> _mod = D('pid');
    }
	
	
	//public function _before_add() {
	public function add() {
		if(IS_POST){
			$pid = I('pid',0,'trim');
			
			$json = \json_decode($pid,true);
			$data = $json['data']['result'];
			if($data){
				$apppid=trim(C('yh_taobao_pid'));
				$apppid=explode('_', $apppid);
				$siteid=$apppid[2];
				$adzoneid=$apppid[3];
				if(!$siteid){
					 $this->error('站点设置中，阿里妈妈PID没有填写');
				}
				
				$tlj=trim(C('yh_taolijin_pid'));
				if($tlj){
					$tlj = explode('_', $tlj);
					$tljid=$tlj[3];
				}
				
				
				$num = 0;
				
				foreach($data as $k=>$v){
					
					if($v['siteId'] == $siteid && $adzoneid !=$v['adzoneId']){
						
						if($tljid && $tljid == $v['adzoneId']){
							continue;
						}
						
						$is = $this->_mod->where(array('pid'=>$v['pid']))->find();
						if(!$is){
						$jsondata = array(
						'pid'=>$v['pid'],
						'status'=>I('status'),
						'update_time'=>NOW_TIME
						);	
						$this->_mod->add($jsondata);
						$num ++;
						}
						
					}
					
					
				}
				
				 $this->success('您已成功添加'.$num.'个PID');
				
				exit;
			}
			
			 $this->error('添加失败：请到阿里妈妈创建PID后再来添加');
		exit;	
			
		}
		
		$this->display();
	}
	
	
	protected function _search(){
        $map = array();
        ($keyword = I('keyword','', 'trim')) && $map['pid'] = array('like', '%' . $keyword . '%');
        return $map;
    }
	
	

}
?>