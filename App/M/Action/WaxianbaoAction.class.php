<?php

namespace M\Action;

class WaxianbaoAction extends BaseAction
{

    public function _initialize()
    {
        parent::_initialize();
    }


    public function  index(){

        $tab = I('tab');
        $this->assign('tab',$tab);

        $data = $this->CallApi($tab?$tab:1,20,1);

        $this->assign('list',$data);

        $this->_config_seo([
            'title' => '挖掘淘宝、京东线报，购物可以省更多'.C('yh_site_name'),
        ]);

        $this->display();

    }


    private function CallApi($topic='',$pagesize='',$page=''){

        $CacheName = md5('waxianbao'.$topic.$pagesize.$page);
        if(false === $data = S($CacheName)){

            $apiurl=$this->tqkapi.'/waxianbao';
            $data=[
                'key'=>$this->_userappkey,
                'time'=>time(),
                'tqk_uid'=>	$this->tqkuid,
            ];
            if($topic){
                $data['topic']= $topic;
            }
            if($pagesize){
                $data['size']= $pagesize;
            }
            if($page){
                $data['page']= $page;
            }
            $token=$this->create_token(trim(C('yh_gongju')), $data);
            $data['token']=$token;
            $result=$this->_curl($apiurl, $data, true);
            $data=json_decode($result, true);

            $result = array();

            foreach ($data['data']['list'] as $k=>$v){

                $item = explode(',',$v['itemIds']);

                $result[$k]['updateTime'] = $v['updateTime'];
                $result[$k]['platform'] = $v['platform'];
                $result[$k]['itemIds'] = $item[0];
                $result[$k]['urls'] = $v['urls'];
                $result[$k]['picUrls'] =  $v['picUrls'];
                $result[$k]['content'] =  $v['content'];
                $result[$k]['contentCopy'] = str_replace(array('[淘口令请转链]','CZ0001'), '', $v['contentCopy']);

            }
            $data = $result;
            S($CacheName,$data,300);
        }

        return $data;


    }

    public  function JdLink(){

        $pid = trim(C('yh_jdpid'));
        $pid = explode('_',$pid);
        $uid = $pid[0];
        $jd_pid = $this->memberinfo['jd_pid'] ? $this->memberinfo['jd_pid'] : $this->GetTrackid('jd_pid');
        $link = base64_decode(I('link'));
        $content = base64_decode(I('content'));

        if (IS_POST && $pid && $link) {

            $apiurl = $this->tqkapi . '/jdconvert';
            $data = [
                'time' => time(),
                'tqk_uid' => $this->tqkuid,
                'uid' => $uid,
                'pid' => $jd_pid?$jd_pid:$pid[2],
                'content' =>$link,
            ];
            $token = $this->create_token(trim(C('yh_gongju')), $data);
            $data['token'] = $token;
            $result = $this->_curl($apiurl, $data, true);
            $data = json_decode($result, true);


            if($data['result']){

                foreach ($data['result'] as $url) {
                    $needle = '[京东请转链]';
                    $content =   substr_replace($content,'<a target="_blank" href="'.$url.'">'.$url.'</a>',strpos($content,$needle),strlen($needle));
                }


            }

        }

        $json=[
            'status'=>200,
            'content'=>$content
        ];

        exit(json_encode($json));


    }


    /**
     * @param $text
     * @return false|string[]
     */
    private  function exeLink($text){

        $pattern = '/https?:\/\/\S+/';

        preg_match_all($pattern, $text, $matches);

        if (!empty($matches[0])) {
            return $matches[0];
        }

        return false;
    }



    /**
     * @return void
     */
    public function WaLink(){

        $id = I('id');
        if (IS_POST && $id) {

            $link = $this->Tbconvert($id,$this->memberinfo);
            $kouLing = kouling('https://gw.alicdn.com/tfs/TB1lOScxDtYBeNjy1XdXXXXyVXa-540-260.png', '活动', $link);

            $json=[
                'status'=>200,
                'link'=>$link,
                'kouling'=>$kouLing
            ];

            exit(json_encode($json));


        }

    }

    /**
     * @return void
     */
    public  function pagelist(){

        $tab =I('tab');
        $page = I('page');

        $this->assign('tab',$tab);

        $data = $this->CallApi($tab?$tab:1,20,$page);

        $this->assign('list',$data);
        $this->display();


    }


}