<?php
namespace Admin\Action;
use Common\Model;

class BannerAction extends BaseAction {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('ad');
    }


    public function _before_index() {
        $big_menu = array(
            'title' => L('ad_add'),
            'iframe' => U('banner/add'),
            'id' => 'add',
            'width' => '520',
            'height' => '151',
        );
       // $this->assign('big_menu', $big_menu);
		$this->assign('img_dir', '/data/upload/site/');
        $res = $this->_mod->select();
        $this->assign('board_list', $res);
    }

    public function _before_add() {
        $this->assign('ad_type_arr', $this->_ad_type);
    }
    
    
 public function hongbao(){
 	
$pid=trim(C('yh_youhun_secret'));
if(!$pid){
$this->ajaxReturn(1,'','<div style="padding-top:10px; padding-left:10px">请到 站点设置  填写拼多多PID</div>');
}
  
$apidata=array(
'tqk_uid'=>$this->tqkuid,
'time'=>time(),
'pid'=>$pid
);
$token=$this->create_token(trim(C('yh_gongju')),$apidata);
$apidata['token']=$token;
$url='http://api.tuiquanke.cn/Pddhongbao';
$json=$this->_curl($url,$apidata,true);
$info=json_decode($json,true);
if($info['result']['error_response']['error_msg']) $this->ajaxReturn(1,'','<div style="padding-top:10px; padding-left:10px">'.$info['result']['error_response']['error_msg'].'</div>');
  
  $info=	$info['result']['rp_promotion_url_generate_response']['url_list'];
   $this->assign('info',$info[0]);
if (IS_AJAX && $info) {
            $response = $this->fetch();
            $this->ajaxReturn(1, '', $response);
        } else {
            $this->ajaxReturn(1, '', '<div style="padding-top:10px; padding-left:10px">获取红包链接失败，请联系推券客客服。</div>');
        }
	
   }	
   
   
   
    public function _before_edit() {
        $id = I('id','0','intval');
//      $board_id = $this->_mod->where(array('id'=>$id))->getField('board_id');
//      $board_info = $this->_adboard_mod->field('name,width,height')->where(array('id'=>$board_id))->find();
//      $this->assign('board_info', $board_info);
       // $this->assign('ad_type_arr', $this->_ad_type);
    }

    public function ajax_upload_img() {
        if (!empty($_FILES['img']['name'])) {
        		if($_FILES['img']){
	            $file = $this->_upload($_FILES['img'], 'site');
	            if($file['error']) {
	            	$this->ajaxReturn(0,$file['info']);
	            } else {
	             $data['img']=$file['pic_path'];
				 $this->ajaxReturn(1, L('operation_success'), $data['img']);
	            }
	   		}
			
			
        } else {
            $this->ajaxReturn(0, L('illegal_parameters'));
        }
    }
}