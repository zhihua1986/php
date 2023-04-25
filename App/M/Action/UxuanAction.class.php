<?php
namespace M\Action;
class UxuanAction extends BaseAction
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function qrcode()
    {
        $data= I('dataurl', '', 'trim');
        $data=htmlspecialchars_decode(base64_decode($data));
        $level = 'L';
        $size = 4;
        vendor("phpqrcode.phpqrcode");
        $object = new \QRcode();
        ob_clean();
        $object->png($data, false, $level, $size, 0);
    }

    public function index(){
        $index = I('tab');
        $data = $this->UxuanTab();
        $this->assign('Tab',$data);
        $PageInfo = $data[$index?$index:0];
        $this->assign('PageInfo',$PageInfo);
        $id = $this->ActivityID($PageInfo['id']);
        $RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
        $LinkInfo = $this->TbkActivity($id,$RelationId,$this->memberinfo['id']);
        $LinkInfo['data']['url'] =  U('uxuan/qrcode').'?dataurl='.base64_encode($LinkInfo['data']['click_url']);
        $LinkInfo['data']['kouling'] = kouling('',$PageInfo['title'],$LinkInfo['data']['click_url']);

        $this->assign('info',$LinkInfo);
        $this->_config_seo([
            'title' => $PageInfo['title'].C('yh_site_name'),
            'keywords'=>$PageInfo['title'],
            'description'=>C('yh_site_name').'提供'.$PageInfo['title'].'免费领取'
        ]);

        $back = $_SERVER["HTTP_REFERER"];
        if ($back && stristr($back, trim(C('yh_headerm_html')))) {
            $this->assign('back', $back);
        }

        $this->display('uxuan');
    }


}