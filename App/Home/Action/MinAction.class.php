<?php
namespace Home\Action;
header("Content-type: text/html; charset=utf-8");
class MinAction extends BaseAction
{

  public function _initialize()
  {
    parent::_initialize();
    $this->_mod = D('items');
  }


  public function checkque(){
   $id = I('id', '', 'trim');
   if(is_numeric($id)){
   $ret=$this->_mod->where(array(
    'num_iid' => $id
    ))->save(array('que'=>1));
   }
   if($ret){
    $json=array(
     'status'=>'ok'
     );	

  }else{
   $json=array(
     'status'=>'no'
     );	

 }

 exit(json_encode($json));

}

public function checkcoupon()
{

  $uptime = I('uptime', '', 'trim');
  $id = I('id', '', 'trim');
  $success = I('success', '', 'trim');
  $amount = I('amount', '', 'trim');
  $retStatus = I('retStatus', '', 'trim');
  if ((NOW_TIME - $uptime) > 86400 && is_numeric($id)) {
    $item = $this->_mod->where(array(
      'id' => $id
      ))->find();
    $appkey = trim(C('yh_taobao_appkey'));
    $appsecret = trim(C('yh_taobao_appsecret'));
    if (! empty($appkey) && ! empty($appsecret)) {
     // import('TopSdk', VENDOR_PATH . 'taobao', '.php');
	  vendor("taobao.taobao");
      $c = new \TopClient();
      $c->appkey = $appkey;
      $c->secretKey = $appsecret;
      $req = new \TbkItemInfoGetRequest();
     // $req->setFields("num_iid,title,seller_id,volume");
      $req->setPlatform("1");
      $req->setNumIids($item['num_iid']);
      $resp = $c->execute($req);
      $resparr = xmlToArray($resp);
      $newitem = $resparr['results']['n_tbk_item'];
      if (count($newitem) > 0 || $newitem === null) {
        if (($success == "true" && $retStatus == 2) || ($success == "true" && $retStatus == 1)) {
          $url = $this->tqkapi."/?m=api&a=checkcoupon_a&key=" . trim(C('yh_gongju')) . "&itemId=" . $item['num_iid'] . "";
           $this->_curl($url);
          $this->_mod->where(array(
            'id' => $id
            ))->delete();
        exit('no');
        } else {
          $updata['up_time'] = NOW_TIME;
          $updata['sellerId'] = $newitem['seller_id'];
          $updata['volume'] = $newitem['volume'];
          $this->_mod->where(array(
            'id' => $id
            ))->save($updata);

        }
      }
    }
  }
}
}
