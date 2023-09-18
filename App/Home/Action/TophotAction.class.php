<?php
namespace Home\Action;
class TophotAction extends BaseAction
{

    public function _initialize() {
        parent::_initialize();
    }

    /**
     * @param $mid
     * @param $id
     * @param $size
     * @param $page
     * @return array
     */
    private function PubApi($mid,$id='',$size='',$page=''){

        $CacheName = md5('PubApi'.$mid.$size.$page.$id);
        if(false === $data = S($CacheName)){

            $appkey = trim(C('yh_taobao_appkey'));
            $appsecret = trim(C('yh_taobao_appsecret'));
            $apppid=trim(C('yh_taobao_pid'));
            $apppid=explode('_', $apppid);
            $AdzoneId=$apppid[3];
            vendor("taobao.taobao");
            $c = new \TopClient();
            $c->appkey = $appkey;
            $c->secretKey = $appsecret;
            $req = new \TbkDgOptimusMaterialRequest();
            $req->setPageSize($size?$size:100);
            $req->setPageNo($page?$page:1);
            $req->setAdzoneId($AdzoneId);
            $req->setMaterialId($mid);
            if($id){
                $req->setFavoritesId($id);
            }
            $resp = $c->execute($req);
            $resp = json_decode(json_encode($resp), true);
            if($mid == '31519' && $resp['result_list']['map_data']['favorites_info']['favorites_list']['favorites_detail']){

                $data = array();
                foreach ($resp['result_list']['map_data']['favorites_info']['favorites_list']['favorites_detail'] as $k=>$v){
                    $data[$k]['id']=$v['favorites_id'];
                    $data[$k]['name']=$v['favorites_title'];
                }
                S($CacheName,$data,300);
                return $data;

            }

            $data = array();

            foreach($resp['result_list']['map_data'] as $k=>$v){
                $data[$k]['quan']=$v['coupon_amount'];
                $data[$k]['coupon_click_url']='';
                $data[$k]['num_iid']=$v['item_id'];
                $data[$k]['title']=$v['title'];
                $data[$k]['coupon_price']=$v['zk_final_price']-$v['coupon_amount'];
                if($v['user_type']=="1"){
                    $data[$k]['shop_type']='B';
                }else{
                    $data[$k]['shop_type']='C';
                }
                $data[$k]['commission_rate']=$v['commission_rate']*100; //比例
                $data[$k]['price']=$v['zk_final_price'];
                $data[$k]['volume']=$v['volume'];
                $data[$k]['pic_url']=$v['white_image']?$v['white_image']:$v['pict_url'];
                $data[$k]['category_id']=$v['category_id'];
            }

            S($CacheName,$data,300);
            return $data;

        }

        return $data;


    }


    /**
     * @return void
     */
    public function index(){
        $back = $_SERVER["HTTP_REFERER"];
        if ($back && stristr($back, trim(C('yh_headerm_html')))) {
            $this->assign('back', $back);
        }
        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1){
            $this->assign('isweixin',true);
        }

        $platform = I('id');
        $op = I('op');

        $data = $this->CallTophot();
        $MyCate =$this->PubApi("31519");

        $this->assign('cate',array_merge($data['result'],$MyCate));


        if(!$platform){
            $platform = $data['result'][0]['id'];
            $op = 1;
        }

        $this->assign('op',$op);
        $this->assign('platform',$platform);

        $r= I('r');
        $this->assign('tips',$data['result'][$r-1]['tips']);
        if(!$r){
            $this->assign('tips',$data['result'][0]['tips']);
        }


        if($op == 1){
            $data = $this->CallTophot('goods',20,1,$platform,$op);
            $list = $data['result'];
        }else{
            $data = $this->PubApi('31539',$platform,20,1);
            $list = $data;

        }

        $this->assign('list',$list);

        $this->_config_seo([
            'title' => '人工精选淘宝天猫热门爆品-'.C('yh_site_name'),
        ]);

        $this->display();

    }


    /**
     * @return void
     */
    public function catelist(){
        $platform = I('id');
        $page = I('page');
        $op = I('op');

        if($op == 1){
            $Datalist = $this->CallTophot('goods',20,$page,$platform,$op);
            $list = $Datalist['result'];
        }else{
            $data = $this->PubApi('31539',$platform,20,$page);
            $list = $data;

        }

        $this->assign('list',$list);
        $this->display('catelist');
    }


}
