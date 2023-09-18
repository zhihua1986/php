<?php
namespace M\Action;
class ElmAction extends BaseAction
{

public function _initialize() {
    parent::_initialize();
}

public function other(){
	
	$ac = I('ac');
	$type = I('type');
	$id = I('id');
	$uid = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
	
	switch($id){
		case '5933':
		$url ='https://qz-m.oaqhsgl.cn/kfc/?platformId=10130';
		break;
		
		case '6680':
		$url ='https://qz-m.oaqhsgl.cn/starbucks/?platformId=10130';
		break;
		
		default:
		$url = 'https://www.didiglobal.com/';
		break;
	}
	if($id==10124 || $id==10127 || $id == 10130){
		$url= "https://i.meituan.com";
	}
    $data = $this->DuomaiLink($id,$url,array('euid'=>$uid?$uid:'m001'));
	if($data['cps_short']){
	redirect($data['cps_short']);
	if($type == 3){
	$link = $data['wx_qrcode'];
	}
	
	$data = array(
	'link'=>$link,
	'type'=>$type
	);
	
	exit(json_encode($data));
	
	}
	
	}


    /**
     * @param $ip
     * @param $type
     * @param $adr
     * @return mixed
     */
    private function Latitude($ip,$type='',$adr=''){
        $apiurl=$this->tqkapi.'/latitude';
        $data=[
            'key'=>$this->_userappkey,
            'time'=>time(),
            'tqk_uid'=>	$this->tqkuid,
            'ip'=>$ip,
            'type'=>$type,
            'adr'=>$adr
        ];
        $token=$this->create_token(trim(C('yh_gongju')), $data);
        $data['token']=$token;
        $result=$this->_curl($apiurl, $data, true);
        $data=json_decode($result, true);
      return $data;
    }

    /**
     * @return void
     */
    public function  shop(){

            if($_SERVER["HTTP_X_FORWARDED_FOR"]){
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }else{
                $ip = get_client_ip();
            }
            $local = I('local');
            $CacheName = 'position'.md5($ip.$this->memberinfo['id']);
            $data = S($CacheName);
            if(!$data['result'] || $local){

                if($local){
                    $data = $this->Latitude($ip,'adr',$local);
                }else{
                    $data = $this->Latitude($ip);
                }

                S($CacheName,$data);
            }

             $this->assign('city',$data['city']);
            $key = I('k');

           $list =  $this->ShopList($data['result'],$key,$this->memberinfo);
            if($list['records']['store_promotion_dto']){
                $this->assign('list',$list['records']['store_promotion_dto']);
                $this->assign('sessionId',$list['session_id']);

            }else{
                S($CacheName,null);
            }

        $this->assign('platform','sdp');
        $this->assign('sokey',I('k'));
            $this->_config_seo([
                'title' => '搜索附近饿了么店铺-'.C('yh_site_name'),
            ]);
            $this->display();

    }

    public function  shops(){
        $k= I('k');
        $cid = I('cid');
        if($_SERVER["HTTP_X_FORWARDED_FOR"]){
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }else{
            $ip = get_client_ip();
        }
        $local = I('local');
        $CacheName = 'position'.md5($ip.$this->memberinfo['id']);
        $data = S($CacheName);
        if(!$data['result'] || $local){
            if($local){
                $data = $this->Latitude($ip,'adr',$local);
            }else{
                $data = $this->Latitude($ip);
            }
            S($CacheName,$data);
        }
        $list =  $this->ShopList($data['result'],$k,$this->memberinfo,$cid);

        $list['city'] =  $data['city'];

        if($list['records']['store_promotion_dto']){
           exit(json_encode($list));
        }
        echo(1);
    }




    /**
     * @param $postion
     * @param $key
     * @param $UserInfo
     * @param $session
     * @return mixed|void
     */
    private function ShopList($postion,$key='',$UserInfo=array(),$session=''){

        vendor("taobao.taobao");
        $c = new \TopClient();
        $appkey = trim(C('yh_elmkey'));
        $secret = trim(C('yh_elmsecret'));
        $pid = trim(C('yh_elmpid'));
        if(strlen($UserInfo['elm_pid'])>5){
            $pid = $UserInfo['elm_pid'];
        }
        $c->appkey = $appkey;
        $c->secretKey = $secret;
        $req = new \AlibabaAlscUnionElemePromotionStorepromotionQueryRequest();
        $query_request = new \PromotionQueryRequest();
        if($session){
            $query_request->session_id=$session;
        }
        $query_request->pid=$pid;
        $query_request->longitude=$postion['lng'];
        $query_request->latitude=$postion['lat'];
         $query_request->page_size="20";
         if($key){
           $query_request->search_content=$key;
         }
        $req->setQueryRequest(json_encode($query_request));
        $resp = $c->execute($req);
        $resparr = json_decode(json_encode($resp), true);
        if($resparr['data']){
            return $resparr['data'];
        }
        exit($resparr['sub_msg']);

    }


    /**
     * @return void
     */
    public function index(){
        $back = $_SERVER["HTTP_REFERER"];
        if ($back && stristr($back, trim(C('yh_headerm_html')))) {
            $this->assign('back', $back);
        }
    $from = I('from');
    $CacheName = 'ElmList'.$from;

    if(false === $data = S($CacheName)){
        $data = $this->CallApi($from);
        S($CacheName,$data);
    }

    $list = array();
    $time = time();
    foreach ($data as $k=>$v){

        if(strtotime($v['time'])>$time){
            $list[$k]['title'] = $v['title'];
            $list[$k]['des'] = $v['des'];
            $list[$k]['id'] = $v['id'];
            $list[$k]['img'] = $v['img'];
        }

    }


     $this->assign('platform',$from?$from:'hot');

    $this->assign('list',$list);

    $this->_config_seo([
        'title' => '饿了么外卖红包每天领-'.C('yh_site_name'),
    ]);

    $this->display('elm');

    }


    public function GetShopLink(){

        vendor("taobao.taobao");
        $c = new \TopClient();
        $appkey = trim(C('yh_elmkey'));
        $secret = trim(C('yh_elmsecret'));
        $pid = trim(C('yh_elmpid'));
        if(strlen($UserInfo['elm_pid'])>5){
            $pid = $UserInfo['elm_pid'];
        }
        $c->appkey = $appkey;
        $c->secretKey = $secret;
        $req = new \AlibabaAlscUnionElemePromotionStorepromotionGetRequest();
        $query_request = new \SingleStorePromotionRequest();
        $query_request->pid=$pid;
        $query_request->shop_id=I('id');
        $query_request->include_wx_img="true";
        $req->setQueryRequest(json_encode($query_request));
        $resp = $c->execute($req);
        $resparr = json_decode(json_encode($resp), true);
        if($resparr['data']){
            exit(json_encode($resparr['data']));
        }
        exit($resparr['sub_msg']);

    }

    /**
     * @return void
     */
    public function GetElmLink(){

         $acid = I('id');
      $data =   $this->CreateElmLink($acid,$this->memberinfo);

      exit(json_encode($data));


    }

    /**
     * @return mixed
     */
    private function  CallApi($type=''){

        $apiurl=$this->tqkapi.'/elmlist';
        $data=[
            'key'=>$this->_userappkey,
            'time'=>time(),
            'tqk_uid'=>	$this->tqkuid,
            'type'=>$type,
        ];
        $token=$this->create_token(trim(C('yh_gongju')), $data);
        $data['token']=$token;
        $result=$this->_curl($apiurl, $data, true);
        $result=json_decode($result, true);
        return $result;
    }


}
