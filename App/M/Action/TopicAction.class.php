<?php
namespace M\Action;

class TopicAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function sharpgoods()
    {
        $topone = S('toptwo_more');
        if ($topone) {
            $this->assign('toptwo', $topone);
        } else {
            $apiurl=$this->tqkapi.'/Tbmaterial/moreitem';
            $apidata=[
                'tqk_uid'=>$this->tqkuid,
                'time'=>time(),
                'size'=>100,
                'sid'=>36846
            ];
            $token=$this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token']=$token;
            $res= $this->_curl($apiurl, $apidata, false);
            $res = json_decode($res, true);
            if ($res && $res['state'] == 200) {
                $topone = S('toptwo_more', $res['data'], 7200);
                $this->assign('toptwo', $res['data']);
            }
        }

        $this->_config_seo([
            'title' => '2020天猫双11实时热销爆款榜 - '. C('yh_site_name'),
            'keywords' => '2020天猫双11',
            'description' => '2020天猫双11实时热销爆款榜'
        ]);

        $this->display();
    }

    public function hotsale()
    {
        $topone = S('topone_more');
        if ($topone) {
            $this->assign('topone', $topone);
        } else {
            $apiurl=$this->tqkapi.'/Tbmaterial/moreitem';
            $apidata=[
                'tqk_uid'=>$this->tqkuid,
                'time'=>time(),
                'size'=>100,
                'sid'=>36229
            ];
            $token=$this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token']=$token;
            $res= $this->_curl($apiurl, $apidata, false);
            $res = json_decode($res, true);
            if ($res && $res['state'] == 200) {
                $topone = S('topone_more', $res['data'], 7200);
                $this->assign('topone', $res['data']);
            }
        }

        $this->_config_seo([
            'title' => '2020天猫双11实时热销爆款榜 - '. C('yh_site_name'),
            'keywords' => '2020天猫双11',
            'description' => '2020天猫双11实时热销爆款榜'
        ]);

        $this->display();
    }

    public function index()
    {
        $topone = S('topone');
        $toptwo = S('toptwo');
        if ($topone && $toptwo) {
            $this->assign('topone', $topone);
            $this->assign('toptwo', $toptwo);
        } else {
            $apiurl=$this->tqkapi.'/Tbmaterial';
            $apidata=[
                'tqk_uid'=>$this->tqkuid,
                'time'=>time(),
                'size'=>12
            ];
            $token=$this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token']=$token;
            $res= $this->_curl($apiurl, $apidata, false);
            $res = json_decode($res, true);
            if ($res && $res['state'] == 200) {
                $topone = S('topone', $res['data'], 7200);
                $toptwo = S('toptwo', $res['result'], 7200);
                $this->assign('topone', $res['data']);
                $this->assign('toptwo', $res['result']);
            }
        }

        $this->_config_seo([
            'title' => '2020天猫双11实时热销爆款榜 - '. C('yh_site_name'),
            'keywords' => '2020天猫双11',
            'description' => '2020天猫双11实时热销爆款榜'
        ]);

        $this->display();
    }
}
