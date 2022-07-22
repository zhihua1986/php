<?php
namespace M\Action;
use Common\Model;
class CanvasAction extends BaseAction{
	public function _initialize() {
		parent::_initialize();
		
	}
	

public function index(){
	
        $images = I('pic_url','','trim');
		$images = str_replace('https','http',$images);
		$image = $this->getImage($images);
		if($image){
			exit(json_encode($image));
}
}

public function del_img(){
       $url = I('address','','trim');
		$file = TQK_IMG_DATA_PATH.$url; 
		$result = @unlink($file); 
		exit(json_encode($file));	
}







	
	
	
	







}