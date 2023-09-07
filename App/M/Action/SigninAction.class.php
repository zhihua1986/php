<?php
namespace M\Action;
use Common\Model\itemsModel;
use Common\Model\helpModel;
use Think\Page;
class SigninAction extends BaseAction
{

    public function help(){
        $help_mod =new helpModel();
        $helps = $help_mod->field('id,title')->select();
        $this->_config_seo(array(
            'title' => $help['title']
        ));
        $this->assign('helps', $helps);

        $this->_config_seo(array(
            'title' => '签到红包使用教程-'.C('yh_site_name'),
        ));

        $this->display();
    }

    public function index(){
        $back = $_SERVER["HTTP_REFERER"];
        if($back && stristr($back,trim(C('yh_headerm_html')))){
            $this->assign('back',$back);
        }
        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1){
            $this->assign('isweixin',true);
        }
        $page	= I('p',0,'number_int');
        $key = I('k');
        $this->assign('sokey',$key);
        $file = 'signin_list'.md5($key);
        if (false === $data = S($file)) {
            $apiurl = $this->tqkapi . '/signredpack';
            $apidata = [
                'tqk_uid' => $this->tqkuid,
                'time' => time(),
                'page' => 0,
                'keyword' => $key
            ];
            $token = $this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token'] = $token;
            $res = $this->_curl($apiurl, $apidata, false);
            $list = json_decode($res, true);
            S($file,$list);
        }else{
            $list = S($file);
        }

        $this->assign('topone',$list);

        $this->_config_seo(array(
            'title' => '淘宝签到红包每天都能省钱-'.C('yh_site_name'),
        ));
        $this->display();


    }


    public function create()
    {
        if(IS_POST){
            $num_iid = I('line');
            $coupon = I('coupon');
            $link = $this->Tbconvert($num_iid,$this->memberinfo,$$coupon);
            $kouling=kouling('', '商品标题', $link);
            if($link){

                $data = array(
                    'code'=>200,
                    'result'=>$kouling

                );

            }else{

                $data = array(
                    'code'=>300,
                    'msg'=>'获取优惠券链接失败！'

                );

            }


        }

        exit(json_encode($data));
    }



    public function pagelist(){


        $page	= I('page',0,'number_int');
        $key = I('k');

        $file = 'signin_list'.md5($key.$page);
        if (false === $data = S($file)) {
            $apiurl = $this->tqkapi . '/signredpack';
            $apidata = [
                'tqk_uid' => $this->tqkuid,
                'time' => time(),
                'page' => $page,
                'keyword' => $key
            ];
            $token = $this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token'] = $token;
            $res = $this->_curl($apiurl, $apidata, false);
            $result = json_decode($res, true);
            $list = $result['data'];
            S($file,$list);
        }else{
            $list = S($file);
        }

        $count = count($list['data']);
        if(count($list['data'])<10){
            $appkey=trim(C('yh_taobao_appkey'));
            $appsecret=trim(C('yh_taobao_appsecret'));
            $apppid=trim(C('yh_taobao_pid'));
            $apppid=explode('_', $apppid);
            $AdzoneId=$apppid[3];

            if(!empty($appkey) && !empty($appsecret) && !empty($AdzoneId)){
                vendor('taobao.taobao');
                $c = new \TopClient();
                $c->appkey = $appkey;
                $c->secretKey = $appsecret;
                $c->format = 'json';
                $req = new \TbkDgMaterialOptionalRequest();
                $req->setAdzoneId($AdzoneId);
                $req->setPlatform("1");
                $req->setPageSize("20");
                if ($key) {
                    $req->setQ((string)$key);
                }
                if ($page>0) {
                    $req->setPageNo("".$page."");
                } else {
                    $req->setPageNo(1);
                }
                if ($sort=='hot') {
                    $req->setSort("total_sales_des");
                } elseif ($sort=='price') {
                    $req->setSort("price_asc");
                } elseif ($sort=='rate') {
                    $req->setSort("tk_rate_des");
                } else {
                    $req->setSort("tk_des");
                }
                $resp = $c->execute($req);
                $resp = json_decode(json_encode($resp), true);
                $resp=$resp['result_list']['map_data'];
                $patterns = "/\d+/";
                foreach($resp as $k=>$v){
                    if($this->FilterWords($v['title']) || !$v['item_id']){
                        continue;
                    }
                    preg_match_all($patterns,$v['coupon_info'],$arr);
                    $quan=$arr[0];
                    $list[$k+$count]['couponmoney']=$v['coupon_amount'];
                    $list[$k+$count]['coupon_click_url']=$v['coupon_share_url']?$v['coupon_share_url']:$v['url'];
                    $list[$k+$count]['itemid']=$v['num_iid'];
                    $list[$k+$count]['itemtitle']=$v['title'];
                    $list[$k+$count]['itemendprice']=$v['zk_final_price']-$list[$k+$count]['quan'];
                    $list[$k+$count]['commission_rate']=$v['commission_rate']; //比例
                    $list[$k+$count]['price']=$v['zk_final_price'];
                    $list[$k+$count]['volume']=$v['volume'];
                    $list[$k+$count]['shopname']=$v['nick'];
                    $list[$k+$count]['itempic']=$v['pict_url'];
                    $list[$k+$count]['category_id']=$v['category_id'];
                }

            }


        }

        $this->assign('topone',$list);
        $this->display();




    }




}