<?php
namespace Admin\Action;
use Common\Model;
class SettingAction extends BaseAction
{
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('setting');
		$cate_data = D('itemscate')->cate_data_cache();
        $this->assign('cate_data', $cate_data);
    }

    public function index() {
        $type = I('type','index','trim');
		
		$index_cids = C('yh_index_cids');
		$this->assign('index_cids', $index_cids);
		if(IS_AJAX){
			$response = $this->fetch($type);
			$this->ajaxReturn(1, '', $response);
		}else{
			$this->display($type);
		}
    }
    
    
       public function sms() {
        $type = I('type','sms','trim');
		
		$index_cids = C('yh_index_cids');
		$this->assign('index_cids', $index_cids);
		if(IS_AJAX){
			$response = $this->fetch($type);
			$this->ajaxReturn(1, '', $response);
		}else{
			$this->display($type);
		}
    }

	public function wechat() {
	        $type = I('type','wechat','trim');
			$index_cids = C('yh_index_cids');
			$this->assign('index_cids', $index_cids);
			if(IS_AJAX){
				$response = $this->fetch($type);
				$this->ajaxReturn(1, '', $response);
			}else{
				$this->display($type);
			}
	    }


public function payment() {
        $type = I('type','payment','trim');
		$index_cids = C('yh_index_cids');
		$this->assign('index_cids', $index_cids);
		if(IS_AJAX){
			$response = $this->fetch($type);
			$this->ajaxReturn(1, '', $response);
		}else{
			$this->display($type);
		}
    }
	   
	   
public function miniapp() {
        $type = I('type','miniapp','trim');
		$index_cids = C('yh_index_cids');
		$this->assign('index_cids', $index_cids);
		if(IS_AJAX){
			$response = $this->fetch($type);
			$this->ajaxReturn(1, '', $response);
		}else{
			$this->display($type);
		}
    }
    
    

	public function cache() {
        $this->display();
    }
    
    public function user() {
        $this->display();
    }
	
	public function ressl(){
	set_time_limit(0);
	$prefix=C("DB_PREFIX");
	$sql="update ".$prefix."items set pic_url=REPLACE(pic_url,'http://','https://')";
	$Model = M();
    $list=$Model->execute($sql);
	exit('ok');
	}
	

public function edit() {
$setting = I('setting', ',');

	if($setting['basklist']){
		$setting['basklist'] = rtrim($setting['basklist'], '|');
	}

        foreach ($setting as $key => $val) {
            $val = is_array($val) ? serialize($val) : $val;
           // $val = is_array($val) ? json_encode($val) : $val;
            $res = $this->_mod->where(array('name' => $key))->save(array('data' => htmlspecialchars_decode($val)));
			if(!$res){
				$datas['name'] = $key;
				$datas['data'] =htmlspecialchars_decode($val);
				$this->_mod->save($datas);
			}
        }
        
        
$appkey=$setting['app_kehuduan'];
if($setting['gongju']){
$this->getopenid(trim($setting['gongju']));
}

if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/Runtime/Data/tuiquanke_settings.php';
$ret=opcache_invalidate($dir,TRUE);
}
$Set= F('tuiquanke_settings');
$Sdata=array(
'bili1'=>$setting['bili1'],
'bili2'=>$setting['bili2'],
'bili3'=>$setting['bili3'],
'headerm_html'=>$setting['headerm_html'],
'taobao_appkey'=>$setting['taobao_appkey'],
'taobao_appsecret'=>$setting['taobao_appsecret'],
'taobao_pid'=>$setting['taobao_pid'],
'taolijin_pid'=>$setting['taolijin_pid'],
'youhun_secret'=>$setting['youhun_secret'],
'jdauthkey'=>$setting['jdauthkey'],
'dmappkey'=>$setting['dmappkey'],
'site_url'=>$setting['site_url'],
'dm_pid'=>$setting['dm_pid'],
'dmsecret'=>$setting['dmsecret']
);

if($appkey && $Sdata != $Set){
$savedata = $Sdata;
$savedata['appkey'] = trim($appkey);
$apiurl='http://api.tuiquanke.cn/getappkey/savebili';
$result=$this->_curl($apiurl,$savedata,true); 
F('tuiquanke_settings',$Sdata);
 
}
        
        
        $type = I('type', 'trim', 'index');
		IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'add');
        $this->success(L('operation_success'));
    }

    public function ajax_mail_test() {
        $email = I('email', 'trim');
        !$email && $this->ajaxReturn(0);
        //发送
        $mailer = mailer::get_instance();
        if ($mailer->send($email, '这是一封测试邮件', '这是一封推券客自动发送的测试邮件')) {
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }


	public function ajax_upload_pem(){
        if(!empty( $_FILES['img']['name'])){
			 $result = $this->_upload($_FILES['img'], 'site');
            if ( $result['error']){
                $this->error( $result['info'] );
            }else{
                $data['img'] = $result['pic_path'];
                $this->ajaxReturn( 1, L( "operation_success" ), $data['img'] );
            }
        }else{
            $this->ajaxReturn( 0, L( "illegal_parameters" ) );
        }
    }

	public function ajax_upload(){
        if(!empty( $_FILES['img']['name'])){
			 $result = $this->_upload($_FILES['img'], 'site');
            if ( $result['error']){
                $this->error( $result['info'] );
            }else{
                $data['img'] = $result['pic_path'];
                $this->ajaxReturn( 1, L( "operation_success" ), C('yh_site_url').$data['img'] );
            }
        }else{
            $this->ajaxReturn( 0, L( "illegal_parameters" ) );
        }
    }

}