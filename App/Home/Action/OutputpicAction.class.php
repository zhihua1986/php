<?php
namespace Home\Action;

class OutputpicAction extends BaseAction {
	
	public function _initialize() {
        parent::_initialize();
    }
	
	public function Outimg(){
	header("Content-type:image/png");	
	$data = base64_decode(I('url'));
	$str = $this->download($data);
	exit($str);
		
	}
	
	
	
	protected function download($url){
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_POST, 0); 
	    curl_setopt($ch,CURLOPT_URL,$url); 
	    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $file = curl_exec($ch);
	    curl_close($ch);
	    return $file;
	}
	
}