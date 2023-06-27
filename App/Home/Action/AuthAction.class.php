<?php
namespace Home\Action;
use Common\Model;
use Common\Model\userModel;
class AuthAction extends BaseAction{

    public function _initialize()
    {
        parent::_initialize();
		if($this->visitor->is_login == false){
			$url=U('login/index','','');
			$this->redirect($url);
		}
		
		}
		
		
		public function  index(){
			if ($this->memberinfo && C('yh_bingtaobao') == 2){
				$url = 'https://mos.m.taobao.com/inviter/register?inviterCode='.trim(C('yh_invitecode')).'&src=pub&app=common&rtag='.$this->memberinfo['id'];
			    $this->assign('inviterCode', $url);
			}
		
		$callback = urldecode(I('callback'));
		$this->assign('callback', $callback);
		
		$this->_config_seo([
		    'title' => '淘宝渠道备案-'.C('yh_site_name'),
		]);
		
		$this->display();
		
		}

    public function  pdd(){

        $callback = urldecode(I('back'));
        $url = urldecode(I('ac'));
        $this->assign('callback', $callback);
        $this->assign('inviterCode', $url);

        $this->_config_seo([
            'title' => '拼多多渠道备案-'.C('yh_site_name'),
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
	
	