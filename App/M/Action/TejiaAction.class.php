<?php
namespace M\Action;

class TejiaAction extends BaseAction
{
    public function index()
    {
        $back = $_SERVER["HTTP_REFERER"];
        if ($back && stristr($back, trim(C('yh_headerm_html')))) {
            $this->assign('back', $back);
        }
		$pid = trim(C('yh_taobao_pid'));
		
		if($this->memberinfo){
			// $R = A("Records");
			// $data= $R ->content($this->memberinfo['id'],$this->memberinfo['id']);
			// $pid = $data['pid'];
		}
		
        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if ((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1) {
            $this->assign('isweixin', true);
        }
        $RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
        $url = 'https://pages.tmall.com/wow/z/sale/new/taoke-jhy?channel=mmlm_jiheye&pid='.$pid.'&relationId='.$RelationId;

        $kouling = kouling('', '特价版', $url);
        $this->assign('hongbao', $kouling);

        $this->_config_seo([
            'title' => '淘宝特价版天天免单0元购-'.C('yh_site_name'),
        ]);
        $this->display();
    }

    public function create()
    {
        if (IS_POST) {
            $RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
            $data = $this->TbkActivity('20150318020004956', $RelationId,$this->memberinfo['id']);
            if ($data['data']['click_url']) {
                $kouling = kouling('', $data['data']['page_name'], $data['data']['click_url']);
            }
        }

        exit(json_encode($kouling));
    }
}
