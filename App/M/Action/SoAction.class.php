<?php
namespace M\Action;

class SoAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->_userdomain=str_replace('/index.php/m', '', C('yh_headerm_html'));
        $this->_userappkey=trim(C('yh_gongju'));
        $this->_pcdomain=C('yh_site_url');
        $this->_avatar=C('yh_site_zhibo');
    }

    public function index()
    {
        $this->assign('status', 'yes');
        $key = I('key', '', 'trim');

        if (!empty($key)) {
            $this->assign('key', $key);

            $appkey=trim(C('yh_taobao_appkey'));
            $appsecret=trim(C('yh_taobao_appsecret'));
            $apppid=trim(C('yh_taobao_pid'));
            $apppid=explode('_', $apppid);
            $AdzoneId=$apppid[3];
            if ($key && $appkey && $appsecret) {
                vendor('taobao.taobao');
                $c = new \TopClient();
                $c->appkey = $appkey;
                $c->secretKey = $appsecret;
                $c->format = 'json';
                $req = new \TbkDgMaterialOptionalRequest();
                $req->setAdzoneId($AdzoneId);
                $req->setPlatform("1");
                $req->setPageSize("10");
                $req->setHasCoupon("true");
                if ($key) {
                    $req->setQ((string)$key);
                }
                $req->setPageNo(1);
                $req->setIncludePayRate30("true");
                $resp = $c->execute($req);
                $resp = json_decode(json_encode($resp), true);
                $resp = $resp['result_list']['map_data'];
                $patterns = "/\d+/";
                $goodslist = [];
                foreach ($resp as $k=>$v) {
                    if ($this->FilterWords($v['title'])) {
                        continue;
                    }
                    $goodslist[$k]['quan']=formatprice($v['coupon_amount']);
                    $goodslist[$k]['coupon_click_url']=$v['coupon_share_url'];
                    $goodslist[$k]['num_iid']=$v['item_id'];
                    $goodslist[$k]['title']=$v['title'];
                    $goodslist[$k]['coupon_price']=round($v['zk_final_price']-$goodslist[$k]['quan'], 2);
                    $goodslist[$k]['rebate']=Rebate1($goodslist[$k]['coupon_price']*$v['commission_rate']/10000);
                    if ($v['user_type']=="1") {
                        $goodslist[$k]['user_type']='B';
                    } else {
                        $goodslist[$k]['user_type']='C';
                    }
                    $goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
                    $goodslist[$k]['price']=round($v['zk_final_price'], 2);
                    $goodslist[$k]['volume']=$v['volume'];
                    $goodslist[$k]['Quan_id']=$v['coupon_id'];
                    $goodslist[$k]['pic_url']=$v['pict_url'];
                    $goodslist[$k]['quankouling']='';
                }
            }

            if ($goodslist) {
                $this->assign('list', $goodslist);
            } else {
                $this->assign('status', 'no');
            }
            $result='yes';
            $this->assign('result', 'yes');
        }

        if ($this->_avatar) {
            $this->assign('avatar', $this->_avatar);
        } else {
            $this->assign('avatar', '/Public/static/images/default_photo.gif');
        }
        $this->display();
    }
}
