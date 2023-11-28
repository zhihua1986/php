<?php
namespace M\Action;
class SoAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();

        if ($this->getRobot()!==false) {
            exit;
        }

    }

    /**
     * @return void
     */
    public  function search(){

        $text = I('key');
        $platFrom = I('platfrom');
        C('TOKEN_ON',true);
        if(!$this->tqkCheckToken($_POST)){
         echo('<script>alert("验证失败，请刷新页面！");window.location.href="'.U('so/index',array('from'=>$platFrom)).'"</script>');
            exit();
        }

        $this->assign('platform',$platFrom?$platFrom:'tb');
        $this->assign('k',$text);


        if($platFrom == 'tb' && (false !== strpos($text, 'tb.cn') || false !== strpos($text, 'u.ltao.com'))  && preg_match('/[\x{4e00}-\x{9fa5}]/u', $text) > 0) {

           $result =  $this->CallApi($text,$platFrom);
            if($result['data']){
                echo('<script>window.location.href="'.U('item/index',array('id'=>$result['data'])).'"</script>');
            }else{
                echo('<script>alert("没查询到此商品的优惠信息!");window.location.href="'.U('so/index',array('from'=>'tb')).'"</script>');
            }

        }elseif ($platFrom == 'tb' && $text){

            echo('<script>window.location.href="'.U('so/index',array('from'=>'tb','k'=>$text)).'"</script>');


        }elseif ($platFrom == 'jd' && (strstr($text, 'jd.com') || strstr($text, 'jingxi.com') || strstr($text, 'jd.hk'))){

            $result =  $this->jditemid($text);
            if($result){
                echo('<script>window.location.href="'.U('jditem/index',array('id'=>$result)).'"</script>');
            }

        }elseif ($platFrom == 'jd'){
            echo('<script>window.location.href="'.U('so/index',array('from'=>'jd','k'=>$text)).'"</script>');
        }elseif ($platFrom == 'pdd' && (strstr($text, 'yangkeduo.com') )){

                $skuid = $this->pdditemid($text);
                if ($skuid) {
                    $info = $this->PddGoodsSearch('','',$skuid);
                    if($info['goodslist'][0]['goods_sign']){
                        $url = U('pdditem/s/'.$info['goodslist'][0]['goods_sign'].'');
                        echo('<script>window.location.href="'.$url.'"</script>');
                    }

                }

        }elseif($platFrom == 'pdd'){
            echo('<script>window.location.href="'.U('so/index').'?from=pdd&k='.$text.'"</script>');
        }elseif ($platFrom == 'vph' && (strstr($text, 't.vip.com') )){
            $url = htmlspecialchars_decode(trim(urldecode($text)));
            if (preg_match('/\=([0-9]{6,})\&brandId/i', $url, $m)) {
                $itemid = $m[1];
                if($itemid){
                    echo('<script>window.location.href="'.U('vph/item',array('id'=>$itemid)).'"</script>');
                }

            }

        }elseif($platFrom == 'vph'){
            echo('<script>window.location.href="'.U('so/index',array('from'=>'vph','k'=>$text)).'"</script>');

        }elseif ($platFrom == 'dy' && (strstr($text, 'douyin.com') )){

            $itemid = $this->GetdouyinId($text);

            if($itemid['item_id']){

                echo('<script>window.location.href="'.U('douyin/item',array('id'=>$itemid['item_id'])).'"</script>');

            }


        }elseif($platFrom == 'dy'){
            echo('<script>window.location.href="'.U('so/index',array('from'=>'dy','k'=>$text)).'"</script>');

        }



        $this->_config_seo(array(
            'title'=>'超级搜索，精准查询全网优惠-'.C('yh_site_name')
        ));


        $this->display('so');

    }


    /**
     * @param $text
     * @return false|mixed
     */
    protected  function GetdouyinId($text){
        $link = $this->getlink($text);
        if($link){
            $where = array(
                'page'=>1,
                'page_size'=>1
            );
            $where['query'] = 'https'.$link;
            $data = $this->DmRequest('cps-mesh.cpslink.douyin.material-products.get',$where);
            if($data['data'][0]['item_id']){
                return $data['data'][0];
            }


        }


        return false;
    }


    /**
     * @param $text
     * @param $platFrom
     * @return mixed
     * 推券客官方声明：禁止私自使用此接口转商品ID，非正常调用会被淘宝联盟处罚。
     * 若监测到非法调用，此接口会被永久禁用。
     */
    private function CallApi($text,$platFrom){
        $apiurl=$this->tqkapi.'/supersearch';
        $data=[
            'key'=>$this->_userappkey,
            'time'=>time(),
            'tqk_uid'=>	$this->tqkuid,
            'platfrom'=>$platFrom,
            'k'=>base64_encode($text),
        ];
        $token=$this->create_token(trim(C('yh_gongju')), $data);
        $data['token']=$token;
        $result=$this->_curl($apiurl, $data, true);
        $result=json_decode($result, true);
        return $result;
    }

    /**
     * @return void
     */
    public function index()
    {

        $key = I('k');
        $platFrom = I('from');
        $this->assign('platform',$platFrom?$platFrom:'tb');
        $this->assign('k',$key);

        if($key){
            $goodslist=array();

            switch ($platFrom){

                case 'jd':
                    $where['title'] = ['like', '%' . $key . '%'];
                    $data = $this->JdGoodsList(20,$where,'id desc',1,false,$key,'');
                    $Datalist = $data['goodslist'];
                    break;

                case 'pdd':

                    $data = $this->PddGoodsSearch('0','',$key,12,'',20,'false');
                    if($data['res']){
                        $back = $_SERVER["HTTP_REFERER"];
                        if ($back) {
                            $url = U('auth/pdd',array('back'=>urlencode(urlencode($back)),'ac'=>urlencode(urlencode($data['res']))));
                            echo('<script>window.location.href="'.$url.'"</script>');
                            exit;
                        }

                    }
                    $Datalist = $data['goodslist'];

                    break;
                case 'vph':

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
                    $Datalist = $data['result']['goodsInfoList'];

                    break;
                case 'dy':

                    $file = 'douyin_list'.md5($key);
                    if (false === $data = S($file)) {

                        $where = array(
                            'page'=>1,
                            'page_size'=>20
                        );
                        if ($key) {
                            $where['query'] = $key;
                        }
                        $data = $this->DmRequest('cps-mesh.cpslink.douyin.material-products.get',$where);
                        S($file,$data);
                    }else{
                        $data = S($file);
                    }
                    $Datalist = $data['data'];
                    break;
                default:
                    $Datalist = $this->GetApiList('',$key,$page,'',[],$goodslist);
                    break;

            }
            $this->assign('list',$Datalist);

        }


        switch ($platFrom){

            case 'dy':
                $placeholder = '请粘贴抖音商品链接到这里搜索';
                break;
            case 'jd':
                $placeholder = '请粘贴京东商品链接到这里搜索';
                break;
            case 'pdd':
                $placeholder = '请粘贴拼多多商品链接到这里搜索';
                break;
            case 'vph':
                $placeholder = '请粘贴唯品会商品链接到这里搜索';
                break;
            default:
                $placeholder = '请粘贴手淘分享内容到这里搜索';
                break;
        }


        $this->assign('placeholder',$placeholder);
        $this->_config_seo(array(
            'title'=>'超级搜索，精准查询全网优惠-'.C('yh_site_name')
        ));

        C('TOKEN_ON',true);

        $this->display('so');
    }
}
