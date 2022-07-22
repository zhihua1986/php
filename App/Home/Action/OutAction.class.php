<?php
namespace Home\Action;
class OutAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->_mod = D('items');
    }
    public function index()
    {
        $id = I('id', '', 'trim');
        $action = I('action');
		$list = $this->_mod->where(array(
                'id' => $id
         ))->Field('quanurl,ems')->find();
          $quanurl=$list['quanurl'];
        if ($action == "quan") {
            if (!empty($quanurl)){
            	

$RelationId = $this->memberinfo['webmaster_pid']?$this->memberinfo['webmaster_pid']:$this->GetTrackid('t_pid');
if($RelationId && $list['ems']==1){
$quanurl=$quanurl.'&relationId='.$RelationId;
}



                echo ('<script>window.location.href="' . htmlspecialchars_decode($quanurl). '"</script>');
            } else {
                echo ('<script>alert("此优惠券活动已失效或者过期！");window.location.href="'.C('yh_site_url').'"</script>');
            }
            exit();
        }
        echo ('<script>alert("骚年你想干什么？")</script>');
        echo ('<script>window.location.href="'.C('yh_site_url').'"</script>');
        exit();
		
    }
}
