<?php
namespace M\Action;
use Common\Model;
use Common\Model\userModel;
class AuthAction extends BaseAction{

    public function _initialize()
    {
        parent::_initialize();

        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if (strpos($useragent, 'micromessenger')>0) {
            $this->assign('weixin', true);
        }
		
		}
		
		
		public function  index(){
			if ($this->memberinfo && C('yh_bingtaobao') == 2){
			    $inviterCode = $this->RelationInviterCode($this->memberinfo);
			    $this->assign('inviterCode', $inviterCode);
			    $this->assign('Tbauth', true);
			}
		
		$callback = urldecode(I('callback'));
		$this->assign('callback', $callback);
		
		$this->_config_seo([
		    'title' => '淘宝渠道备案-'.C('yh_site_name'),
		]);
		
		$this->display();
		
		}
		
		
		public function Getrelation()
		{
		    $url=$this->tqkapi."/getrelationid";
		    $data=[
		        'key'=>$this->_userappkey,
		        'time'=>time(),
		        'tqk_uid'=>	$this->tqkuid,
		    ];
		    $token=$this->create_token(trim(C('yh_gongju')), $data);
		    $data['token']=$token;
		    $data=$this->_curl($url, $data, true);
		    $result=json_decode($data, true);
		    if ($result['code'] == 200) {
		        $Data = [];
		        $Mod = new UserModel();
		        if ($result['result']['rtag'] == $this->memberinfo['id']) {
		            $Data = [
		                'webmaster_pid' =>$result['result']['relation_id'],
		                'webmaster'=>1,
		            ];
		        } else {
		            foreach ($result['result'] as $k=>$v) {
						 if(is_array($v)){
		                if ($v['rtag'] == $this->memberinfo['id']) {
		                    $Data = [
		                        'webmaster_pid' =>$v['relation_id'],
		                        'webmaster'=>1,
		                    ];
		                    break;
		                }
						}
		            }
		        }
		
		        if ($Data) {
		            $res=$Mod->where(['id'=>$this->memberinfo['id']])->save($Data);
		            if ($res) {
		                $this->visitor->wechatlogin($this->memberinfo['openid']); //更新用户信息
		                $json= ['status'=>1];
		            }
		        } else {
		            $json= [
		                'status'=>2
		            ];
		        }
		    } else {
		        $json= [
		            'status'=>2
		        ];
		    }
		    exit(json_encode($json));
		}
		
    }
	
	