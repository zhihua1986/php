<?php
namespace M\Action;
use Common\Model;
class DouyinAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();

        if ($this->getRobot() !== false || C('yh_dm_cid_dy')!=1) {
            exit;
        }
    }

    /**
     * @return array[]
     */
    protected function DouYinCate(){
        $data = [
            '1'=>['女装','20005'],
            '2'=>['男装','20009'],
            '3'=>['美妆','20085,20038,32617,20029'],
            '4'=>['家居','20040,20070,20071,20072,20073,20074,20076'],
            '5'=>['母婴','20025,20028,20032,20033,20055,20067,20068'],
            '6'=>['鞋包','20006,20010,20011,20046,20052'],
            '7'=>['数码电器','20000,20035,20044,20049,20065,20081,20083,20087,20098'],
            '8'=>['运动文体','20003,20026,20027,20069,20095']
        ];

        return $data;

    }

    /**
     * @return void
     */
    public function fen(){

        $cate = $this->DouYinCate();
        $this->assign('catetree',$cate);
        $this->assign('sokey',$key);

        $file = 'douyin_fen'.md5($key.$cid);
        if (false === $data = S($file)) {

            $apiurl = $this->tqkapi . '/Douyingoods';
            $apidata = [
                'tqk_uid' => $this->tqkuid,
                'time' => time(),
                'page' => 1,
                'ac' => 1
            ];
            $token = $this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token'] = $token;
            $res = $this->_curl($apiurl, $apidata, false);
            $data = json_decode($res, true);

            S($file,$data);
            }else{
                $data = S($file);
            }

        $this->assign('list',$data['data']['list']);

        $this->_config_seo(array(
            'title' => '抖音'.$cate[$cid][0].'1分购 - '. C('yh_site_name'),
        ));

        $this->display();


    }


    /**
     * @return void
     */

    public function index(){


        $key = I('k');
        $cid = I('cid');

        $cate = $this->DouYinCate();
        $this->assign('catetree',$cate);
        $this->assign('sokey',$key);
        $this->assign('cid',$cid);

        $file = 'douyin_list'.md5($key.$cid);
        if (false === $data = S($file)) {

            $where = array(
                'page'=>1,
                'page_size'=>20
            );

            if ($cid) {
                $where['category_id'] = $cate[$cid][1];
            }

            if ($key) {
                $where['query'] = $key;
            }

            $data = $this->DmRequest('cps-mesh.cpslink.douyin.material-products.get',$where);
            S($file,$data);
        }else{
            $data = S($file);
        }


        $this->assign('list',$data['data']);

        $this->_config_seo(array(
            'title' => '抖音'.$cate[$cid][0].'精选商品 - '. C('yh_site_name'),
        ));

        $this->display();

    }



    /**
     * @return void
     */
    public  function  item(){
        $id = I('id');
        $sp = I('sp');
        $where = array(
            'ids'=>$id
        );

        if($sp){
            $this->assign('sp',$sp);
        }

        $info = $this->DmRequest('cps-mesh.cpslink.douyin.product.detail.get',$where);
        $info = $info['data'][0];

        $info = [
            'title'=> $info['item_title'],
            'commission_rate' =>floatval($info['commission_rate'])*100, //要统一算法
            'pict'=> $info['item_picture'],
            'coupon_price'=> $info['item_final_price'],
            'price'=> $info['item_price'],
            'quan'=> $info['coupon_price']?$info['coupon_price']:0,
            'sold'=> $this->floatNumber($info['item_volume']),
            'itemid'=> $info['item_id'],
            'jumpmode'=>'miling',
            'ext'=>$info['item_url'],
            'sellName'=>$info['seller_name'],
            'detailImages'=> $info['item_small_pictures'],
        ];
        $this->assign('item',$info);
        $this->_config_seo(array(
            'title'=>$info['title'].'-'.C('yh_site_name')
        ));
        $file = 'Dorlike_m' .  md5($info['itemid']);
        if (false === $orlike = S($file)) {
            $cid = $this->GetTags($info['title'], 2);
            $where=[
                'title'=>array('like',array('%'.$cid[0].'%','%'.$cid[1].'%'))
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

            $goodsid = I('id');
            $url = 'https://haohuo.jinritemai.com/ecommerce/trade/detail/index.html?id='.$goodsid.'&origin_type=open_platform&pick_source=v.Epyxi';
            $data = $this->DuomaiLink('14882',$url,array('euid'=>$this->memberinfo['id']?$this->memberinfo['id']:'m001'));
            exit(json_encode($data));


        }



    }

    /**
     * @return void
     */
    public function out(){

        $goodsid = I('id');
        $url = 'https://haohuo.jinritemai.com/ecommerce/trade/detail/index.html?id='.$goodsid.'&origin_type=open_platform&pick_source=v.Epyxi';
        $data = $this->DuomaiLink('14882',$url,array('euid'=>$this->memberinfo['id']?$this->memberinfo['id']:'m001'));
        $this->assign('jump',$data['deep_link']);

        $this->display();
    }

    /**
     * @return void
     */
    public function fenlist(){
        $page	= I('page');
        $fileName = 'douyin_fenlist'.md5($key.$page.$cid);
       // if (false === $data = S($fileName)) {
            $apiurl = $this->tqkapi . '/Douyingoods';
            $apidata = [
                'tqk_uid' => $this->tqkuid,
                'time' => time(),
                'page'=>$page,
                'ac' => 1
            ];
            $token = $this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token'] = $token;
            $res = $this->_curl($apiurl, $apidata, false);
            $data = json_decode($res,true);
            S($fileName,$data);

//        }else{
//            $data = S($fileName);
//        }

        $this->assign('list',$data['data']['list']);

        $this->display('fenlist');
    }


    /**
     * @return void
     */
    public function catelist(){
        $page	= I('page');
        $cid = I('cid');
        $key    = trimall(I("k",'','htmlspecialchars'));
        $key    = urldecode($key);


        $fileName = 'douyin_catelist'.md5($key.$page.$cid);

        if (false === $data = S($fileName)) {
            $where = array(
                'page'=>$page,
                'page_size'=>20
            );
            if ($cid) {
                $cate = $this->DouYinCate();
                $where['category_id'] = $cate[$cid][1];
            }
            if ($key) {
                $where['query'] = $key;

            }
            $data = $this->DmRequest('cps-mesh.cpslink.douyin.material-products.get',$where);
            S($fileName,$data);
        }else{
            $data = S($fileName);
        }

        $this->assign('list',$data['data']);

        $this->display('catelist');
    }



}