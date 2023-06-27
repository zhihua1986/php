<?php
namespace Home\Action;
use Common\Model;
use Think\Page;
use Common\Model\itemsModel;

class VphAction extends BaseAction{
    public function _initialize() {
        parent::_initialize();

        if ($this->getRobot()!==false || !C('yh_vphpid')) {
            exit;
        }
    }

    /**
     * @return void
     */
    public function index(){

        $key = I('k');
        $page = I('p', 1, 'intval');
        if($key && !$this->memberinfo['id']){
            echo('<script> alert("请登录后再搜索！");window.history.back()</script>');
            exit;
        }

        $file = 'vph_list'.md5($key);
        if (false === $data = S($file)) {
        $apiurl = $this->tqkapi . '/Vphgoodslist';
        $apidata = [
            'tqk_uid' => $this->tqkuid,
            'time' => time(),
            'page'=>0,
            'sokey'=>$key
        ];
        $token = $this->create_token(trim(C('yh_gongju')), $apidata);
        $apidata['token'] = $token;
        $res = $this->_curl($apiurl, $apidata, false);
        $data = json_decode($res,true);
        S($file,$data);
        }else{
            $data = S($file);
        }

        $this->assign('list',$data['result']['goodsInfoList']);
        if($key){
            $this->assign('nextPage',$data['result']['page']);
            $this->assign('sokey',$key);
        }else{
        $this->assign('nextPage',$data['result']['nextPageOffset']);
        }

        $this->_config_seo(array(
            'title' => '唯品会'.$cateinfo['name'].'优惠券 - '. C('yh_site_name'),
        ));

        $this->display();

    }

    /**
     * @return void
     */
    public  function  item(){
        $id = I('id');
        $cacheName = 'vphItem'.md5($id.$this->memberinfo['id']);
        if (false === $data = S($cacheName)) {
            $apiurl = $this->tqkapi . '/Vphgoodsdetail';
            $apidata = [
                'tqk_uid' => $this->tqkuid,
                'time' => time(),
                'id' => $id,
                'queryDetail' => false,
                'queryExclusiveCoupon' => true,
                'queryCpsInfo' => true,
                'openId' => $this->memberinfo['id'],
                'chanTag' => C('yh_vphpid')
            ];
            $token = $this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token'] = $token;
            $res = $this->_curl($apiurl, $apidata, false);
            $data = json_decode($res, true);
            S($cacheName,$data);
        }else{
            $data = S($cacheName);
        }
        $item = $data['result'][0];
        $tag = $this->GetTags($item['goodsName'], 4);
        $this->assign('tag',$tag);
        $this->assign('item',$item);
        $this->_config_seo(array(
            'title'=>$data['result'][0]['goodsName'].'-'.C('yh_site_name')
        ));

        $file = 'orlike_m' .  md5($item['goodsId']);
        if (false === $orlike = S($file)) {
            $where=[
                'title'=>array('like',array('%'.$tag[0].'%','%'.$tag[1].'%'))
            ];
            $orlike = M('items')->where($where)->field('id,volume,num_iid,quan,commission_rate,title,pic_url,coupon_price,price,shop_type')->limit('0,6')->order('id desc')->select();
            S($file, $orlike);
        } else {
            $orlike = S($file);
        }

        $this->assign('orlike', $orlike);

        $this->display();


    }

    /**
     * @return void
     */
    public  function shortLink(){

        if(IS_POST){

        $id = I('id');
        $apiurl = $this->tqkapi . '/Vphcpslink';
        $apidata = [
            'tqk_uid' => $this->tqkuid,
            'time' => time(),
            'id' => $id,
            'openId' => $this->memberinfo['id'],
            'chanTag' => C('yh_vphpid')
        ];
        $token = $this->create_token(trim(C('yh_gongju')), $apidata);
        $apidata['token'] = $token;
        $res = $this->_curl($apiurl, $apidata, false);

            $data = json_decode($res,true);


            if($data['result']['urlInfoList'][0]['url']){

                $json= [
                    'code'=>200,
                    'result'=>base64_encode($data['result']['urlInfoList'][0]['url'])
                ];
                exit(json_encode($json));
            }



        }



    }


    public function catelist(){
        $page	= I('page');
        $key    = trimall(I("k",'','htmlspecialchars'));
        $key    = urldecode($key);

        if($key && !$this->memberinfo['id']){
//    echo('<script>alert("请登录再搜索！");</script>');
            echo 1;
            exit;
        }

        if($page>5 && !$this->memberinfo['id']){
//    $this->assign('islogin','请登录后再浏览');
            echo 1;
            exit;
        }

        $fileName = 'vph_catelist'.md5($key.$page);

        if (false === $data = S($fileName)) {
            $apiurl = $this->tqkapi . '/Vphgoodslist';
            $apidata = [
                'tqk_uid' => $this->tqkuid,
                'time' => time(),
                'page' => $page,
                'sokey' => $key
            ];
            $token = $this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token'] = $token;
            $res = $this->_curl($apiurl, $apidata, false);
            $data = json_decode($res, true);
            S($fileName,$data);
        }else{
            $data = S($fileName);
        }

        $this->assign('list',$data['result']['goodsInfoList']);

        $this->display('catelist');
    }








}