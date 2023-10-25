<?php
namespace M\Action;

class PromotionAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
    }


    public function index(){

    $tab = I('tab')?I('tab'):4;
    $this->assign('tab',$tab);

    $back = $_SERVER["HTTP_REFERER"];
    if ($back && stristr($back, trim(C('yh_headerm_html')))) {
        $this->assign('back', $back);
    }
    $data = $this->CallApi($tab);

    $this->assign('list',$data);

        $this->_config_seo([
            'title' => '2023淘宝天猫京东拼多多双11活动专场 - '. C('yh_site_name'),
            'keywords' => '双十一超级红包免费领取页面,双十一京享红包',
            'description' => ''
        ]);

        $this->display();

    }


    public function PromotionLink(){

    $tab = I('tab');
    $id = base64_decode(I('id'));
    $id = str_replace('&amp;', '&', $id);
    switch ($tab){

        case '4':

          $RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
          $result = $this->TbkActivity($id,$RelationId,$this->memberinfo['id']);
          if($result['data']['click_url']){

           $data = kouling('https://gw.alicdn.com/tfs/TB1lOScxDtYBeNjy1XdXXXXyVXa-540-260.png', '活动', $result['data']['click_url']);

          }

            break;

        case '5':

            $data =  $this->jdpromotion($id);

            break;

        case '6':

            $result =  $this->phoneBill($this->memberinfo['id'],39998,$id);
            if($result['resource_url_response']['single_url_list']['we_app_web_view_short_url']){

                $data = $result['resource_url_response']['single_url_list']['we_app_web_view_short_url'];

            }
            break;

        default:
            break;

    }

        $json=[
            'status'=>200,
            'tab'=>$tab,
            'result'=>$data
        ];

        exit(json_encode($json));


    }


    /**
     * @return mixed
     */
    private function  CallApi($type=''){

        $CacheName = md5('promotion'.$type);
        if(false === $res = S($CacheName)){
        $apiurl=$this->tqkapi.'/promotion';
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
            $res = $result;
            S($CacheName,$res,300);
        }

        return $res;

    }



}

