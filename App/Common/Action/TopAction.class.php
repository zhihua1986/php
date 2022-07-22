<?php
namespace Common\Action;
use Common\Model\orderModel;
use Common\Api\Weixin;
use Duomai\CpsClient\Client;
use Duomai\CpsClient\Endpoints\Products;
/**
 * 前台控制器基类
 */
class TopAction extends FuncAction
{
    protected function _initialize()
    {
        if (false === ($setting = S('setting'))) {
            $setting = D('setting')->setting_cache();
        }
        C($setting);
        $this->params=I('param.');
        $Now=NOW_TIME;
        $this->jdappkey = trim(C('yh_jdappkey'));
        $this->jdsecretkey= trim(C('yh_jdsecretkey'));
        $this->jdbaseurl = 'https://api.jd.com/routerjson';
    }

	protected function mergeUser($A,$B){
	$table = array('tqk_balance','tqk_basklistlogo','tqk_finance','tqk_jdorder','tqk_mtorder','tqk_order','tqk_pddorder','tqk_records','tqk_usercash');
		foreach($table as $k => $v)
		{
			 $sql = 'update '.$v.' set uid = '.$B.' where uid = '.$A;
			 $result=M()->execute($sql);
		}
	}

	protected function DmRequest($action,$param){
		vendor("duomai.autoload");
		$config = [
			"host" => "https://open.duomai.com/apis/",
			"auth" => [
				"app_key" => ''.trim(C('yh_dmappkey')).'',
				"app_secret" => ''.trim(C('yh_dmsecret')).'',
			]
		];
		$client = new Client($config);
		$data = $client->Request($action,$param);
		return $data;
	}


	protected function GetDmOrder($stime,$etime){
		vendor("duomai.autoload");
		$config = [
			"host" => "https://open.duomai.com/apis/",
			"auth" => [
				"app_key" => ''.trim(C('yh_dmappkey')).'',
				"app_secret" => ''.trim(C('yh_dmsecret')).'',
			]
		];
		$client = new Client($config);
		$stime = date('Y-m-d H:i:s',$stime);
		$etime = date('Y-m-d H:i:s',$etime);
		$data = $client->OrderList($stime,$etime,1,200);
		return $data;
	}
	
	protected function floatNumber($number){
	    $length = strlen($number);  //数字长度
	    if($length > 8){ //亿单位
	        $str = substr_replace(strstr($number,substr($number,-7),' '),'.',-1,0)."亿";
	    }elseif($length >4){ //万单位
	        //截取前俩为
	        $str = substr_replace(strstr($number,substr($number,-3),' '),'.',-1,0)."万";
	    }else{
	        return $number;
	    }
	    return $str;
	}
 
	protected function DouCate(){
		
		$data = array(
		'20000'=>array('name'=>'3C数码','cid'=>'20000,20007,20087,20111'),
		'20005'=>array('name'=>'服饰内衣','cid'=>'20005,20009,20062'),
		'20026'=>array('name'=>'运动户外','cid'=>'20026,20027,20052,20053'),
		'20006'=>array('name'=>'箱包鞋帽','cid'=>'20006,20010,20011,20046,20093'),
		'20029'=>array('name'=>'美妆护肤','cid'=>'20029,20038,20040,20085,20093'),
		'20070'=>array('name'=>'家居日用','cid'=>'20070,20073,20074,20115'),
		'20028'=>array('name'=>'母婴用品','cid'=>'20028,20032,20033,20055,20067,20068'),
		'20103'=>array('name'=>'生活服务','cid'=>'20103,20117,31928,20106'),
		'20065'=>array('name'=>'生活电器','cid'=>'20065,20075,20083,20099'),
		);
		
		return $data;
	}

	protected function DuomaiLink($productID,$url,$array=[]){
		
		$uid = $array['euid'];
		$douyin_openid = md5($array['douyin_openid']);
		$urlcode = md5($url);
		$cachename = 'DuomaiLink_'.$productID.$uid.$douyin_openid.$urlcode;
		$content = S($cachename);
		if($content){
		return $content;
		}
		vendor("duomai.autoload");
		$config = [
		    "host" => "https://open.duomai.com/apis/",
		    "auth" => [
		        "app_key" => ''.trim(C('yh_dmappkey')).'',
		        "app_secret" => ''.trim(C('yh_dmsecret')).'',
		    ]
		];
		$client = new Client($config);
		$LinkInfo = $client->chanLink(trim(C('yh_dm_pid')),'0',$productID,$url,$array,false);
		if($LinkInfo && $LinkInfo['ads']){
			S($cachename,$LinkInfo);
		}
		return $LinkInfo;
	}

    protected function Fstatic($id)
    {
        switch ($id) {
            case 2:
            return '待付款';
            break;
            case 1:
            return '已付款';
            break;
            default:
            return '异常';
            break;
        }
    }
	
	protected function CashMethod($id)
	{
	    switch ($id) {
	        case 2:
	        return '支付宝';
	        break;
	        case 1:
	        return '微信';
	        break;
	        default:
	        return '未知';
	        break;
	    }
	}

    public function _empty()
    {
        $this->_404();
    }

    protected function check_verify($code, $id = '')
    {
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    public function verify()
    {
        ob_end_clean();
        $Verify =    new \Think\Verify();
        $Verify->fontSize = 30;
        $Verify->length   = 4;
        $Verify->useCurve = false;
        $Verify->useNoise = false;
        $Verify->entry();
    }

		 //生成文章唯一的数字id
	protected function getArticleId() {
			$endtime=1356019200;
			$curtime=time();//当前时间戳
			$newtime=$curtime-$endtime;//新时间戳
			$rand=rand(0,99);//两位随机
			$all=$rand.$newtime;
			return $all;
	} 

    protected function jditemid($url)
    {
        $url = htmlspecialchars_decode(trim(urldecode($url)));
        $itemid = '';
        if (strpos($url, 'jd.com') !== false) {
            if (preg_match('/\/([0-9]{6,})\.html/i', $url, $m)) {
                $itemid = $m[1];
                return $itemid;
            }
        }
		
		if (strpos($url, 'jd.hk') !== false) {
		    if (preg_match('/\/([0-9]{6,})\.html/i', $url, $m)) {
		        $itemid = $m[1];
		        return $itemid;
		    }
		}
		
        if (strpos($url, 'jd.com') !== false) {
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $arr);
            $query = $arr;
            if ($query['sku']) {
                return $query['sku'];
            }
			if ($query['wareId']) {
			    return $query['wareId'];
			}
        }
		
		
		
        if (strpos($url, 'jingxi.com') !== false) {
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $arr);
            $query = $arr;
            if ($query['sku']) {
                return $query['sku'];
            }
        }
        return false;
    }

	protected function Toplijin($page = 1,$size = 10){
		$appkey=trim(C('yh_taobao_appkey'));
		$appsecret=trim(C('yh_taobao_appsecret'));
		$TljPid = trim(C('yh_taolijin_pid'));
		$TbPid = trim(C('yh_taobao_pid'));
		$apppid = $TljPid?$TljPid:$TbPid;
		$apppid=explode('_', $apppid);
		$AdzoneId=$apppid[3];
		vendor("taobao.taobao");
		$c = new \TopClient();
		$c->appkey = $appkey;
		$c->secretKey = $appsecret;
		$c->format = 'json';
		$req = new \TbkDgMaterialOptionalRequest();
		$req->setAdzoneId($AdzoneId);
		$req->setPlatform("1");
		$req->setMaterialId("41703");
		$req->setQ("true");
		if($page>0){
		$req->setPageNo("".$page."");
		}else{
		$req->setPageNo(1);	
		}
		$req->setIsTmall("true");
		$req->setHasCoupon("true");
		$req->setPageSize("".$size."");
		$req->setNpxLevel("2");
		$req->setStartTkRate("2000");
		$resp = $c->execute($req);
		$resp = json_decode(json_encode($resp), true);
		$resp=$resp['result_list']['map_data'];	
		$goodslist = array();
		foreach($resp as $k=>$v){
		$quan = $v['coupon_amount'];
		$coupon_price = $v['zk_final_price']- $quan;
		$commission = round($coupon_price*($v['commission_rate']/10000), 2);
		$linjin = round($commission*(C('yh_taolijin')/100), 2);
		$lim = (float)C('yh_jingpintie');
		if($lim>1 && $linjin>$lim){
			$linjin = $lim;
		}
		if($this->FilterWords($v['title']) || $linjin < 1){
		continue;
		}
		$goodslist[$k]['quan']=$v['coupon_amount'];
		$goodslist[$k]['coupon_click_url']=$v['coupon_share_url']?$v['coupon_share_url']:$v['url'];
		$goodslist[$k]['num_iid']=$v['item_id'];
		$goodslist[$k]['title']=$v['title'];
		$goodslist[$k]['coupon_id']=$v['coupon_id'];
		$goodslist[$k]['coupon_price']=$v['zk_final_price']-$goodslist[$k]['quan'];
		if($v['user_type']=="1"){
		$goodslist[$k]['shop_type']='B';	
		}else{
		$goodslist[$k]['shop_type']='C';	
		}
		$goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
		$goodslist[$k]['price']=$v['zk_final_price'];
		$goodslist[$k]['volume']=$v['volume'];
		$goodslist[$k]['pic_url']=$v['pict_url'];
		$goodslist[$k]['category_id']=$v['category_id'];
		$goodslist[$k]['taolijin']=$linjin;
		$goodslist[$k]['zhuanxiang']=round($goodslist[$k]['coupon_price']-$goodslist[$k]['taolijin'],2);
		}
		
		return array_values($goodslist);
		
	}


    protected function pdditemid($url)
    {
        $url = htmlspecialchars_decode(trim(urldecode($url)));
        $itemid = '';
        if (strpos($url, 'yangkeduo.com') !== false) {
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $arr);
            $query = $arr;
            if (isset($query['goods_id'])) {
                $itemid = $query['goods_id'];
                return $itemid;
            }
        }
        return false;
    }

    protected function bindorder()
    {
        $pid = trim(C('yh_taolijin_pid'));
        $apppid=explode('_', $pid);
        $AdzoneId=$apppid[3];
        $mod= new orderModel();
        $stime = strtotime("-30 day");
        if ($AdzoneId) {
            $sql ='select a.oid,a.webmaster_rate as rate,a.fuid,a.guid,a.id as uid,b.id from tqk_user as a LEFT JOIN tqk_order as b ON a.oid=b.oid OR (a.webmaster_pid=b.relation_id AND b.relation_id>0) where b.ad_id<>'.$AdzoneId.' and b.settle=0 and b.uid=0 and b.add_time>'.$stime;
        } else {
            $sql ='select a.oid,a.webmaster_rate as rate,a.fuid,a.guid,a.id as uid,b.id from tqk_user as a LEFT JOIN tqk_order as b ON a.oid=b.oid OR (a.webmaster_pid=b.relation_id AND b.relation_id>0) where b.settle=0 and b.uid=0 and b.add_time>'.$stime;
        }
        $Model = M();
        $list_child=$Model->cache(true, 5 * 60)->query($sql);
        foreach ($list_child as $k=>$v) {
            $data=[
                'uid'=>$v['uid'],
                'fuid'=>$v['fuid'],
                'guid'=>$v['guid'],
                'leve1'=>$v['rate'] ? $v['rate'] : trim(C('yh_bili1')),
                'leve2'=>trim(C('yh_bili2')),
                'leve3'=>trim(C('yh_bili3')),
            ];
            $map=[
                'id'=>$v['id']
            ];
            $mo=$mod->where($map)->save($data);
        }
    }

    protected function randStr($uid, $len = 6, $format = 'ALL')
    {
        switch ($format) {
        case 'ALL':
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        break;
        case 'CHAR':
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        break;
        case 'HIGHER':
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        break;
        case 'LOWER':
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        break;
        case 'NUMBER':
        $chars = '0123456789';
        break;
        default:
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
        break;
    }
        mt_srand((double) microtime() * 1000000 * $uid);
        $password = "";
        while (\strlen($password) < $len) {
            $password .= substr($chars, (mt_rand() % \strlen($chars)), 1);
        }

        return $password;
    }
	
	
 protected function pddPromotionUrl($id,$search_id='',$uid='',$from=''){
		 $where=[
			    'type'=>'pdd.ddk.goods.promotion.url.generate',
			    'data_type'=>'JSON',
			    'timestamp'=>$this->msectime(),
			    'client_id'=>trim(C('yh_pddappkey')),
				//'custom_parameters'=>'tqklm2',
				//'generate_authority_url'=>'true',
				'generate_we_app'=>'true',
				'goods_sign_list'=>'["'.$id.'"]',
				'p_id'=>trim(C('yh_youhun_secret')),
			];
			if($search_id){
			$where['search_id']=$search_id;
			}
			if($uid){
			$where['custom_parameters']=$uid;
			}
			if($from){
			$where['generate_authority_url']='true';	
			}
			$where['sign']=$this->create_pdd_sign(trim(C('yh_pddsecretkey')), $where);
			$pdd_api='http://gw-api.pinduoduo.com/api/router';
			$result=$this->_curl($pdd_api, $where, true);
			$list=json_decode($result, true);
			$data = $list['goods_promotion_url_generate_response']['goods_promotion_url_list'][0];
			if($data){
				return $data;
			}
		   return false;
	}
	

    protected function PddGoodsDetail($id,$sign=true)
    {
        $where=[
            'type'=>'pdd.ddk.goods.detail',
            'data_type'=>'JSON',
            'timestamp'=>$this->msectime(),
            'client_id'=>trim(C('yh_pddappkey')),
        ];
		if($sign){
		$where['goods_sign'] = $id;
		}else{
		$where['goods_id_list'] = '['.$id.']';
		}
        $where['sign']=$this->create_pdd_sign(trim(C('yh_pddsecretkey')), $where);
        $pdd_api='http://gw-api.pinduoduo.com/api/router';
        $result=$this->_curl($pdd_api, $where, true);
        $list=json_decode($result, true);
		if($list['error_response']['sub_msg']){
		F('data/pdddetail',$list['error_response']['sub_msg']);
		}
		$result = $list['goods_detail_response']['goods_details'][0];
        if ($result) {
		$result['goods_id']=$result['goods_id'];
		$result['goods_name']=$result['goods_name'];
		$result['goods_desc']=$result['goods_desc'];
		$result['goods_thumbnail_url']=str_replace("http://", "https://", $result['goods_thumbnail_url']);
		$result['goods_image_url']=$result['goods_gallery_urls'];
		$result['sold_quantity']=$result['sales_tip']?$result['sales_tip']:0;
		$result['min_group_price']=$result['min_group_price']/100;
		$result['min_normal_price']=$result['min_normal_price']/100;
		$result['mall_name']=$result['mall_name'];
		$result['category_id']=$result['cat_id'];
		$result['coupon_discount']=$result['coupon_discount']/100;
		$result['coupon_total_quantity']=$result['coupon_total_quantity'];
		$result['coupon_remain_quantity']=$result['coupon_remain_quantity'];
		$result['coupon_start_time']=$result['coupon_start_time'];
		$result['coupon_end_time']=$result['coupon_end_time'];
		$result['promotion_rate']=$result['promotion_rate'];
		$result['goods_sign']=$result['goods_sign'];
		$result['coupon_price']=round($result['min_group_price']-$result['coupon_discount'],2);
		$result['quanurl']='';
			
            return $result;
        }
		
        return false;
    }

    protected function JdGoodsDetail($id)
    {
        $sname = 'jddetail_'.$id;
        $result = S($sname);
        if ($result) {
            return $result;
        }
        $apiurl=$this->tqkapi.'/jdgoodsdetail';
        $data=[
            'key'=>$this->_userappkey,
            'time'=>time(),
            'tqk_uid'=>$this->tqkuid,
            'id'=>$id,
        ];
        $token=$this->create_token(trim(C('yh_gongju')), $data);
        $data['token']=$token;
        $result=$this->_curl($apiurl, $data, true);
        $Extend=json_decode($result, true);
        if ($Extend['data'][0]) {
            S($sname, $Extend['data'][0]);
            return  $Extend['data'][0];
        }

        return false;
    }

    protected function PddExtendedSearch($key, $sort=6)
    {
        if ($this->FilterWords($key)) {
            return false;
        }
        $sname = 'pddsearch_'.md5($key);
        $result = S($sname);
        if ($result) {
            return $result;
        }
        $apiurl=$this->tqkapi.'/pddsearch';
        $data=[
            'key'=>$this->_userappkey,
            'time'=>time(),
            'tqk_uid'=>$this->tqkuid,
            'keyword'=>$key,
        ];
        $token=$this->create_token(trim(C('yh_gongju')), $data);
        $data['token']=$token;
        $result=$this->_curl($apiurl, $data, true);
        $Extend=json_decode($result, true);
        if ($Extend['result']['goods_search_response']['goods_list']) {
            S($sname, $Extend['result']['goods_search_response']['goods_list']);
            return $Extend['result']['goods_search_response']['goods_list'];
        }

        return false;
    }


    protected function PddGoodsCats()
    {
        $data = [
			'7'=>'百亿补贴',
			'10564'=>'精选爆品',
			'24'=>'品牌精选',
            '14'=>'女装',
            '4'=>'母婴',
            '15'=>'百货',
            '18'=>'电器',
            '1281'=>'鞋包',
            '1282'=>'内衣',
            '16'=>'美妆',
            '743'=>'男装',
            '818'=>'家纺',
            '2478'=>'文具',
			 '13'=>'生鲜水果',
            '2048'=>'汽车用品',
            '1917'=>'五金配件',
            '2974'=>'家具用品'
        ];

        return $data;
    }

    protected function PddGoodsSearch($opid='', $page='', $key='', $sort=0, $tagid='', $size=40, $hash='true')
    {
        $where=[
            'type'=>'pdd.ddk.goods.search',
            'timestamp'=>$this->msectime(),
            'with_coupon'=>$hash,
            'sort_type'=>$sort,
			'pid'=>trim(C('yh_youhun_secret')),
            'page_size'=>$size,
            'client_id'=>trim(C('yh_pddappkey')),
        ];
		
		$tags = array(7,24,10564,31);
		
        if ($opid && !in_array($opid,$tags)) {
            $where['opt_id'] = $opid;
        }
        if ($key && !$this->hasEmoji($key)) {
            $where['keyword'] = $key;
        }
        if ($page) {
            $where['page'] = $page;
        }
        if ($tagid || in_array($opid,$tags)) {
			$tagid = $tagid?$tagid:$opid;
            $where['activity_tags'] = '['.$tagid.']';
        }
		
		$uid = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
		
		if($uid){
		$where['custom_parameters']=$uid;
		}
        $where['sign']=$this->create_pdd_sign(trim(C('yh_pddsecretkey')), $where);
		
        $pdd_api='http://gw-api.pinduoduo.com/api/router';
        $result=$this->_curl($pdd_api, $where, true);
        $items_list=json_decode($result, true);
		if($items_list['goods_search_response']['goods_list']){
		$today=date('Ymd');
		$goodslist=array();
		foreach($items_list['goods_search_response']['goods_list'] as $k=>$v){
			
			if($this->FilterWords($v['goods_name'])){
			continue;
			}
			
			$goodslist[$k]['id']=$v['goods_id'];
			$goodslist[$k]['goods_id']=$v['goods_id'];
			$goodslist[$k]['title']=$v['goods_name'];
			$goodslist[$k]['pic_url']=$v['goods_thumbnail_url'];
			$goodslist[$k]['coupon_price']=round($v['min_group_price']/100-$v['coupon_discount']/100,2);
			$goodslist[$k]['price']=round($v['min_normal_price']/100,2);
			$goodslist[$k]['group_price']=round($v['min_group_price']/100,2);
			$goodslist[$k]['promotion_rate']=$v['promotion_rate'];
			$goodslist[$k]['commission_rate']=$v['promotion_rate']/10;
			$goodslist[$k]['quan']=$v['coupon_discount']/100;
			$goodslist[$k]['volume']=$v['sales_tip']?$v['sales_tip']:0;
			$goodslist[$k]['end_time']=$v['coupon_end_time'];
			$goodslist[$k]['goods_sign']=$v['goods_sign'];
			$goodslist[$k]['itemid']=$v['goods_sign'];
			$goodslist[$k]['search_id']=$v['search_id'];
			if(C('APP_SUB_DOMAIN_DEPLOY')){
			$goodslist[$k]['linkurl']=U('/pdditem/',array('s'=>$v['goods_sign']));
			}else{
			$goodslist[$k]['linkurl']=U('pdditem/index',array('s'=>$v['goods_sign']));
			}
			
		}
		}
		
		
		if($count<$size && $key){
		 $Extend = $this->PddExtendedSearch($key,$ExtOrder);
		if($Extend){
		foreach($Extend as $k=>$v){
		$goodslist[$k+$count]['id']=$v['goods_id'];
		$goodslist[$k+$count]['goods_id']=$v['goods_id'];
		$goodslist[$k+$count]['title']=$v['goods_name'];
		$goodslist[$k+$count]['pic_url']=$v['goods_thumbnail_url'];
		$goodslist[$k+$count]['coupon_price']=round($v['min_group_price']/100-$v['coupon_discount']/100,2);
		$goodslist[$k+$count]['price']=round($v['min_normal_price']/100,2);
		$goodslist[$k+$count]['group_price']=round($v['min_group_price']/100,2);
		$goodslist[$k+$count]['promotion_rate']=$v['promotion_rate'];
		$goodslist[$k+$count]['quan']=$v['coupon_discount']/100;
		$goodslist[$k+$count]['volume']=$v['sales_tip'];
		$goodslist[$k+$count]['end_time']=$v['coupon_end_time'];
		$goodslist[$k+$count]['search_id']=$v['search_id'];
		$goodslist[$k+$count]['goods_sign']=$v['goods_sign'];
		if(C('APP_SUB_DOMAIN_DEPLOY')){
		$goodslist[$k+$count]['linkurl']=U('/pdditem/',array('s'=>$v['goods_sign']));
		}else{
		$goodslist[$k+$count]['linkurl']=U('pdditem/index',array('s'=>$v['goods_sign']));
		}
		}
		
		}
		
		
			
		}
		
		$data = array(
		'goodslist'=>array_values($goodslist),
		'count'=> $items_list['goods_search_response']['total_count']
		);
		
		
        return $data;
    }

    protected function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime =  (float)sprintf('%.0f', ((float) $msec + (float) $sec) * 1000);
        return $msectime;
    }

    protected function create_pdd_sign($key, $data=[])
    {
        $ServerToken=$key;
        ksort($data); //数组按照键首字母排序
        foreach ($data as $k=>$v) {
            $ServerToken.=$k.$v;
        }
        $ServerToken=$ServerToken.$key;
        $sign=md5($ServerToken);
        return strtoupper($sign);
    }
	
	
	protected function JdGoodsList($size,$where,$order,$page=1,$iscount='',$key='',$categoryid=''){
		$mod = M('jditems')->cache(true, 5 * 60);
		$start = abs($size * ($page - 1));
		
		if(!is_numeric($categoryid)){
		$items_list = $mod->where($where)->field('id,pict,owner,title,itemid,cate_id,coupon_price,price,quan,item_type,comments,commission_rate')->order($order)->limit($start . ',' . $size)->select();
		if($iscount){
		$total_count =$mod->where($where)->count();
		}
		}
		
		if($items_list){
		$today=date('Ymd');
		$goodslist=array();
		foreach($items_list as $k=>$v){
		if($this->FilterWords($v['title'])){
		continue;
		}
		$goodslist[$k]['id']=$v['id'];
		$goodslist[$k]['itemid']=$v['itemid'];
		$goodslist[$k]['title']=$v['title'];
		$goodslist[$k]['pic_url']=$v['pict'];
		$goodslist[$k]['coupon_price']=round($v['coupon_price'],2);
		$goodslist[$k]['price']=round($v['price'],2);
		$goodslist[$k]['commission_rate']=$v['commission_rate'];
		$goodslist[$k]['quan']=$v['quan'];
		$goodslist[$k]['item_type']=$v['item_type'];
		$goodslist[$k]['owner']=$v['owner'];
		$goodslist[$k]['cate_id']=$v['cate_id'];
		$goodslist[$k]['comments']=$v['comments'];	
		if($today==date('Ymd',$v['addtime'])){
		$goodslist[$k]['is_new']=1;	
		}else{
		$goodslist[$k]['is_new']=0;		
		}
		if(C('APP_SUB_DOMAIN_DEPLOY')){
		$goodslist[$k]['linkurl']=U('/jditem/',array('id'=>$v['id']));
		}else{
		$goodslist[$k]['linkurl']=U('jditem/index',array('id'=>$v['id']));
		}
			
		}
		}
		
		$count=count($items_list);
		
		if($count<$size && $key){
		
		 $Extend = $this->JdExtendedSearch($key,$page,$categoryid);
		if($Extend){
		foreach($Extend as $k=>$v){
		$goodslist[$k+$count]['goods_id']=$v['goods_id'];
		$goodslist[$k+$count]['itemid']=$v['goods_id'];
		$goodslist[$k+$count]['title']=$v['goods_name'];
		$goodslist[$k+$count]['pic_url']=$v['goods_image'];
		//$goodslist[$k+$count]['coupon_price']=round($v['coupon_price'],2);
		$goodslist[$k+$count]['price']=round($v['goods_price'],2);
		$goodslist[$k+$count]['coupon_price']=round($v['goods_price']-$v['coupon_amount'],2);
		$goodslist[$k+$count]['commission_rate']=str_replace('%','',$v['rate']);
		$goodslist[$k+$count]['quan']=$v['coupon_amount'];
		$goodslist[$k+$count]['quanurl']=$v['coupon_url'];
		$goodslist[$k+$count]['item_type']=$v['item_type'];
		$goodslist[$k+$count]['owner']=$v['owner'];
		$goodslist[$k+$count]['cate_id']=$v['goods_cate1'];
		$goodslist[$k+$count]['comments']=$v['OrderCountIn30Days'];
		if(C('APP_SUB_DOMAIN_DEPLOY')){
		$goodslist[$k+$count]['linkurl']=U('/jditems/',array('id'=>$v['goods_id']));
		}else{
		$goodslist[$k+$count]['linkurl']=U('jditems/index',array('id'=>$v['goods_id']));
		}
			
		}
		
		}
		
			
		}
		
		$data = array(
		'goodslist'=>array_values($goodslist),
		'total'=>$total_count
		);
		if($goodslist){
			return $data;
		}
		
		return false;
		
		
	}

    protected function JdExtendedSearch($key,$page=1,$cid='')
    {
        if ($this->FilterWords($key) || $this->hasEmoji($key)) {
            return false;
        }
        $Url = 'https://www.duomai.com/api/jd.query.php';
        $Data = [
            'page'=>$page,
            'isCoupon'=>1,
            'keyword'=>$key,
        ];
		if($cid){
			$Data['cid1'] = $cid;
		}
        $Result = $this->_curl($Url, $Data);
        $Result = json_decode($Result, true);
        if ($Result['data']) {
            return $Result['data'];
        }

        return false;
    }

    protected function _curl($url, $data = [], $is_post = false, $cookie = null, $referer = null)
    {
        set_time_limit(30);
        $curl = curl_init();
        if ($data && $is_post == false) {
            $url .= '?' . http_build_query($data);
        }
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); //
        curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, 'Content-Type: application/json');
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if ($is_post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_REFERER, C('yh_site_url'));
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090730 Firefox/3.5.2 GTB5');
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($curl);
       // var_dump(curl_error($curl));
        curl_close($curl);
        if (empty($result)) {
            $result = false;
        }

        return $result;
    }

    protected function check_token($key)
    {
        $http_token = $_SERVER['HTTP_TOKEN'];
        $url_token=$this->params['token'];
        $token = $http_token ? $http_token : $url_token;
        if (!$token) {
            exit($this->return_json('401', 'Token不能为空'));
        }
        $apptoken=$token;
        $result=$this->params;
        unset($result['token']);
        $ServerToken='';
        foreach ($result as $k=>$v) {
            $ServerToken.=md5($v);
        }
        $ServerToken=md5($ServerToken .'_'.$key);

        if ($apptoken!=$ServerToken) {
            $this->header_403();
            exit($this->return_json('403', '无效的Token', $result));
        }
    }

    protected function check_tb_token($key)
    {
        $token=$this->params['token'];
        if (!isset($token) || empty($token)) {
            exit($this->return_json('401', 'Token不能为空'));
        }
        $apptoken=$this->params['token'];
        $result = [];
        $result['uid']=$this->params['uid'];
        $result['from']=$this->params['from'];
        $ServerToken='';
        foreach ($result as $k=>$v) {
            $ServerToken.=md5($v);
        }
        $ServerToken=md5($ServerToken .'_'.$key);
        if ($apptoken!=$ServerToken) {
            $this->header_403();
            exit($this->return_json('403', '无效的Token', $result));
        }
    }

    protected function create_token($key, $data=[])
    {
        $ServerToken='';
        foreach ($data as $k=>$v) {
            $ServerToken.=md5($v);
        }
        $ServerToken=md5($ServerToken .'_'.$key);
        return $ServerToken;
    }

    protected function delorder()
    {
        if (\function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir=$basedir.'/data/runtime/Data/history/delorder.php';
            $ret=opcache_invalidate($dir, true);
        }
        $delorder = F('history/delorder');
        $NowTimes=NOW_TIME;
        if (($NowTimes-$delorder)>600) {
            $updata=[
                'status'=>2
            ];
            $mod_order=M('order');
            $lasttime = mktime(0, 0, 0, date("m"), date("d")-20, date("Y"));
            $upwhere['status']=1;
            $upwhere['add_time'] = ['lt', $lasttime];
            $res=$mod_order->where($upwhere)->save($updata);
            F('history/delorder', $NowTimes);
        }
    }

    protected function remoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return \strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str
       );
        $str = preg_replace('# #', '', $str);
        return $str;
    }

    protected function return_json($code, $msg='', $data='')
    {
        $json=[
            'status'=>$code,
            'msg'=>$msg,
            'result'=>$data,
        ];
        return json_encode($json);
    }
	
	
	protected function TbkActivity($id,$relationid="",$uid=""){
		$appkey = trim(C('yh_taobao_appkey'));
		$appsecret = trim(C('yh_taobao_appsecret'));
		$apppid=trim(C('yh_taobao_pid'));
		if($uid){
			$R = A("Records");
			$data= $R ->content($uid,$uid); 
			$apppid = $data['pid'];
		}
		$apppid=explode('_', $apppid);
		$AdzoneId=$apppid[3];
		vendor('taobao.taobao');
		$c = new \TopClient();
		$c->appkey = $appkey;
		$c->secretKey = $appsecret;
		$req = new \TbkActivityInfoGetRequest();
		$req->setAdzoneId($AdzoneId);
		if($relationid){
		$req->setRelationId("".$relationid."");	
		}
		$req->setActivityMaterialId("".$id."");
		$resp = $c->execute($req);
		$resp = json_decode(json_encode($resp), true);
		return $resp;
		
	}
	

    public function getItem($num_iid, $activityId)
    {
        $appkey = trim(C('yh_taobao_appkey'));
        $appsecret = trim(C('yh_taobao_appsecret'));
        if (! empty($appkey) && ! empty($appsecret) && !empty($num_iid)) {
            vendor('taobao.taobao');
            $c = new \TopClient();
            $c->appkey = $appkey;
            $c->secretKey = $appsecret;
            $req = new \TbkItemInfoGetRequest();
          //  $req->setFields("num_iid,user_type,title,seller_id,volume,nick,pict_url,reserve_price,zk_final_price,item_url");
            $req->setPlatform("1");
            $req->setNumIids($num_iid);
            $resp = $c->execute($req);
            $resparr = xmlToArray($resp);
            $contents = $resparr['results']['n_tbk_item'];
        }

        if ($contents) {
            $info = [];
            $info['title'] = $contents['title'];
            $info['volume'] = $contents['volume'];
            $info['price'] = $contents['zk_final_price'];
            $info['pic_url'] = $contents['pict_url'];
            $info['pic_url'] = str_replace('_320x320.jpg', "", $info['pic_url']);
            $info['sellerId'] = $contents['seller_id'];
            $info['nick'] = $contents['nick'];
            if ($contents['user_type'] == "1") {
                $info['shop_type'] = "B";
            } else {
                $info['shop_type'] = "C";
            }
            $info['num_iid'] = $num_iid;
            $info['coupon_start_time'] = date('Y-m-d H:i', time());
            $info['coupon_end_time'] = date('Y-m-d H:i', time() + 86400 * 7);
            //			$descUrl = 'http://hws.m.taobao.com/cache/mtop.wdetail.getItemDescx/4.1/?data=%7B%22item_num_id%22%3A%22' . $num_iid . '%22%7D';
//          $source = $this->_curl($descUrl);
//          if (!$source) {
//              $source = file_get_contents($descUrl);
//          }
//          $result_data = json_decode($source, true);
//          $dinfo = array();
//          $num = $result_data['data']['images'];
//          for ($i = 0; $i < count($num); $i++) {
//              $images = $i + 1;
//              $desc[$images] = $num[$i];
//              $desc[$images] = '<img class="lazy" src=' . $desc[$images] . '>';
//          }
//          $info['desc'] = $desc[1] . '' . $desc[2] . '' . $desc[3] . '' . $desc[4] . '' . $desc[5] . '' . $desc[6] . '' . $desc[7] . '' . $desc[8] . '' . $desc[9] . '' . $desc[10] . '' . $desc[11] . '' . $desc[12] . '' . $desc[13] . '' . $desc[14] . '' . $desc[15] . '' . $desc[16] . '' . $desc[17] . '' . $desc[18] . '' . $desc[19] . '' . $desc[20] . '' . $desc[21] . '' . $desc[22] . '' . $desc[23] . '' . $desc[24] . '' . $desc[25] . '' . $desc[26] . '' . $desc[27] . '' . $desc[28] . '' . $desc[29] . '' . $desc[30];
//
            return $info;
        }
        return false;
    }

    protected function header_403()
    {
        header('HTTP/1.1 403 Forbidden');
    }
    protected function checkTime($time)
    {
        if ($time<=1) {
            $this->header_403();
            exit($this->return_json('403', '时间校验异常'));
        }
        if (abs(time()-$time)>300) {
            $this->header_403();
            exit($this->return_json('403', '服务器时间异常'));
        }
    }

    /**
     * 图片上传处理
     * @param [String] $path [保存文件夹名称]
     * @param [String] $thumbWidth [缩略图宽度]
     * @param [String] $thumbHeight [缩略图高度]
     * @param mixed $FILES
     * @param mixed $thumb
     * @return [Array] [图片上传信息]
     */

    protected function _upload($FILES, $path, $thumb=[])
    {
        $obj = new \Think\Upload();// 实例化上传类
        $obj->maxSize = C('yh_attr_allow_size')*1024*1024 ;// 设置附件上传大小
        $obj->savePath =C('yh_attach_path').$path.'/'; // 设置附件上传目录
        $obj->exts = explode(',', C('yh_attr_allow_exts'));// 设置附件上传类型
        $obj->saveName = ['uniqid', ''];//文件名规则
        $obj->rootPath =ROOT_PATH.'/';
        $obj->replace = true;//存在同名文件覆盖
        $obj->autoSub = true;//使用子目录保存
        $obj->subName  = ['date', 'Ym'];//子目录创建规则，
        $info = $obj->uploadOne($FILES);
        if (!$info) {
            return ['error' =>0, 'info'=> $obj->getError()];
        }
        if ($thumb) {  //生成缩略图
            $image = new \Think\Image();
            $thumb_file =ROOT_PATH.'/'.$info['savepath'] . $info['savename'];
            $save_path =ROOT_PATH.'/'.$info['savepath'] . 'thumb_' . $info['savename'];
            $image->open($thumb_file)->thumb($thumb['width'], $thumb['height'], \Think\Image::IMAGE_THUMB_FILLED)->save($save_path);
            @unlink($thumb_file); //上传生成缩略图以后删除源文件

            return [
                'status' => 1,
                'savepath' => $info['savepath'],
                'savename' => $info['savename'],
                'pic_path' => $info['savepath'] . $info['savename'],
                'mini_pic' => '/'.$info['savepath'] . 'thumb_' .$info['savename']
            ];
        }

        //   foreach($info as $file) {
        return [
            'status' => 1,
            'savepath' => $info['savepath'],
            'savename' => $info['savename'],
            'pic_path' => '/'.$info['savepath'].$info['savename']
        ];
        // }
    }

    protected function getthemonth($date)
    {
        $firstday = date('Y-m-01', $date);
        $lastday = date("Y-m-d", strtotime("first day of next month", $date));
        return [strtotime($firstday), strtotime($lastday)];
    }

    protected function _404($url = '')
    {
        if ($url) {
            redirect($url);
        } else {
            header("HTTP/1.0  404  Not Found");
            $this->display('Index/404');
            // send_http_status(404);
            exit;
        }
    }
    public function http($url)
    {
        set_time_limit(0);
        $result = file_get_contents($url);
        if ($result === false) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
        }
        if (empty($result)) {
            $result = false;
        }
        return $result;
    }
    public function get_id($url)
    {
        $id = 0;
        $parse = parse_url($url);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $params);
            if (isset($params['id'])) {
                $id = $params['id'];
            } elseif (isset($params['item_id'])) {
                $id = $params['item_id'];
            } elseif (isset($params['default_item_id'])) {
                $id = $params['default_item_id'];
            } elseif (isset($params['amp;id'])) {
                $id = $params['amp;id'];
            } elseif (isset($params['amp;item_id'])) {
                $id = $params['amp;item_id'];
            } elseif (isset($params['amp;default_item_id'])) {
                $id = $params['amp;default_item_id'];
            }
        }
        return $id;
    }

    protected function getRobot()
    {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        $searchEngineBot = [
            'Googlebot'=>'google',
            'mediapartners-google'=>'google',
            'Baiduspider'=>'baidu',
            'msnbot'=>'msn',
            'dotbot'=>'dotbot',
            'youdaobot'=>'yodao',
            'yahoo! slurp'=>'yahoo',
            'yahoo! slurp china'=>'yahoo',
            'iaskspider'=>'iask',
            'sogou web spider'=>'sogou',
            'sogou push spider'=>'sogou',
            'sosospider'=>'soso',
            'bingbot'=>'bing',
            'yandexbot'=>'yan',
            'ahrefsbot'=>'ahrefs',
            '360Spider'=>'360',
            'sitemapx'=>'sitemapx',
            'spider'=>'other',
            'alibaba.security'=>'alibaba',
            'bot'=>'bot',
            'dotbot'=>'dotbot',
            'MJ12bot'=>'MJ12bot',
            'DeuSu'=>'DeuSu',
            'Swiftbot'=>'Swiftbot',
            'YandexBot'=>'YandexBot',
            'YisouSpider'=>'YisouSpider',
            'jikeSpider'=>'jikeSpider',
            'EasouSpider'=>'EasouSpider',
            'oBot'=>'oBot',
			'uni-app'=>'other',
			'MSIE'=>'ohter',
			'Adsbot/3.1'=>'other',
			'toutiao.com'=>'ohter',
			'Alibaba.Security.Heimdall'=>'alibaba',
            'Sogou'=>'Sogou',
            'semrush'=>'semrush',
            'FlightDeckReports Bot'=>'FlightDeckReports',
            'crawler'=>'other',
        ];
        $spider = strtolower($_SERVER['HTTP_USER_AGENT']);
        foreach ($searchEngineBot as $key => $value) {
            if (strpos($spider, $key)!== false) {
                return true;
            }
        }
        return false;
    }
	
	protected function hasEmoji($str)
	  {
	      $text = json_encode($str); 
	      if(preg_match("/(\\\u[ed][0-9a-f]{3})/i", $text)){
					return true;
				}
				return false;
	  }

protected function _itemid($url){
$url = htmlspecialchars_decode(trim(urldecode($url)));
if(strpos($url, 'taobao.com') !== false || strpos($url, 'tmall.com') !== false || strpos($url, 'tmall.hk') !== false){
	$query = parse_url($url, PHP_URL_QUERY);
	parse_str($query, $arr);
	$query = $arr;
	if(isset($query['id'])){
		$itemid = $query['id'];
	}
	elseif(isset($query['itemId'])){
		$itemid = $query['itemId'];
	}
	else{
		if(preg_match('/taobao.com\/i(.+?).htm/is', $url, $m)){
			$itemid = $m[1];
		}
		if(preg_match('/\/([0-9]{11,})\.htm/i', $url, $m)){
			$itemid = $m[1];
		}
	}
	return $itemid;
}	
   
return false;
}

    protected function getlink($content)
    {
        $pregstr = "/(:\/\/[A-Za-z0-9_#?.&=\/]+)([\x{4e00}-\x{9fa5}])?(\s)?/u";
        if (preg_match($pregstr, $content, $matchArray)) {
            $body = $matchArray[1];
            return $body;
        }
    }
    protected function paramOrder($params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v)) {
                $v = $this->characet($v, 'utf-8');
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "$v";
                } else {
                    $stringToBeSigned .=  "$k" . "$v";
                }
                $i++;
            }
        }
        unset($k, $v);
        return $stringToBeSigned;
    }
    //为空检查
    protected function checkEmpty($value)
    {
        if (!isset($value)) {
            return true;
        }
        if ($value === null) {
            return true;
        }
        if (trim($value) === "") {
            return true;
        }
        return false;
    }
    protected function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = 'utf-8';
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }
    protected function paramSign($params)
    {
        $str = $this->jdsecretkey.$params.$this->jdsecretkey;
        $sign = md5($str);
        return strtoupper($sign);
    }

    protected function apiSign($param)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->jdbaseurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    protected function verificat()
    {
        $openid = $_SESSION['user']['openid'];
        $json=[
            'status'=>'out',
            'msg'=>'登录超时'
        ];
        if (!$openid) {
            exit(json_encode($json));
        }
    }

    protected function jddetail($skuId)
    {
        $business_data = [
            'skuIds' => $skuId
        ];
        $param_json = json_encode($business_data);
        $system_data = [
            'method' => 'jd.union.open.goods.promotiongoodsinfo.query',
            'app_key' => $this->jdappkey,
            'timestamp' => date("Y-m-d H:i:s"),
            'format' => 'json',
            'v' => '1.0',
            'sign_method' => 'md5',
            'sign' => '',
            '360buy_param_json' => $param_json
        ];
        $orderData = $this->paramOrder($system_data);
        $sign = $this->paramSign($orderData);
        $system_data['sign'] = $sign;
        $res = $this->apiSign($system_data);
        $arr =  json_decode($res, true);
        $res = $arr['jd_union_open_goods_promotiongoodsinfo_query_responce']['queryResult'];
        $arr =  json_decode($res, true);
        if ($arr['code']==200) {
            return $arr['data'][0];
        }

        return false;
    }

    protected function jdorderquery($startTime, $endTime)
    {
        $business_data = [
            'orderReq' => [
                'pageIndex' => 1,
                'pageSize' => 500,
                'type' => 3,
                'startTime' => date('Y-m-d H:i:s', $startTime),
                'endTime'=> date('Y-m-d H:i:s', $endTime)
            ]
        ];
        $param_json = json_encode($business_data);
        $system_data = [
            'method' => 'jd.union.open.order.row.query',
            'app_key' => $this->jdappkey,
            'timestamp' => date("Y-m-d H:i:s"),
            'format' => 'json',
            'v' => '1.0',
            'sign_method' => 'md5',
            'sign' => '',
            '360buy_param_json' => $param_json
        ];
        $orderData = $this->paramOrder($system_data);
        $sign = $this->paramSign($orderData);
        $system_data['sign'] = $sign;
        $res = $this->apiSign($system_data);
        $arr =  json_decode($res, true);
        return ($arr['jd_union_open_order_row_query_responce']['queryResult']);
    }
	
	protected function JdGoodsQuery($skuId){
		
	$business_data = [
	    'goodsReq' => [
	        'skuIds' => [$skuId]
	    ]
	];
	
	$param_json = json_encode($business_data);
	$system_data = [
	    'method' => 'jd.union.open.goods.bigfield.query',
	    'app_key' => $this->jdappkey,
	    'timestamp' => date("Y-m-d H:i:s"),
	    'format' => 'json',
	    'v' => '1.0',
	    'sign_method' => 'md5',
	    'sign' => '',
	    '360buy_param_json' => $param_json
	];
	$orderData = $this->paramOrder($system_data);
	$sign = $this->paramSign($orderData);
	$system_data['sign'] = $sign;
	$res = $this->apiSign($system_data);
	$arr =  json_decode($res, true);
	$res = $arr['jd_union_open_goods_bigfield_query_responce']['queryResult'];
	$arr =  json_decode($res, true);
	return $arr;
	}

    protected function jdpromotion($skuId, $coupon='',$jd_pid='')
    {
        $pid = trim(C('yh_jdpid'));
		if(!$jd_pid){
		$jd_pid = $this->memberinfo['jd_pid'] ? $this->memberinfo['jd_pid'] : $this->GetTrackid('jd_pid');
        }
		$apppid=explode('_', $pid);
        $business_data = [
            'promotionCodeReq' => [
                'materialId' => $skuId,
                'siteId' => $apppid[1],
                'positionId' => $jd_pid ? $jd_pid : $apppid[2],
                'couponUrl' => $coupon,
                'ext1'=> ''
            ]
        ];
		
        $param_json = json_encode($business_data);
        $system_data = [
            'method' => 'jd.union.open.promotion.common.get',
            'app_key' => $this->jdappkey,
            'timestamp' => date("Y-m-d H:i:s"),
            'format' => 'json',
            'v' => '1.0',
            'sign_method' => 'md5',
            'sign' => '',
            '360buy_param_json' => $param_json
        ];
        $orderData = $this->paramOrder($system_data);
        $sign = $this->paramSign($orderData);
        $system_data['sign'] = $sign;
        $res = $this->apiSign($system_data);
        $arr =  json_decode($res, true);
        $res = $arr['jd_union_open_promotion_common_get_responce']['getResult'];
        $arr =  json_decode($res, true);
        return $arr['data']['clickURL'];
    }

    protected function _ajax_jd_order_insert($item)
    {
        $result =$this->ajax_jd_publish_stat($item);

        return $result;
    }
	
	protected function _ajax_meituan_order($item)
	{
	    $result =$this->ajax_meituan_publish_stat($item);
	
	    return $result;
	}
	
	protected function _ajax_duomai_order($item)
	{
	    $result =$this->ajax_duomai_publish_stat($item);
	
	    return $result;
	}

    protected function _ajax_pdd_order_insert($item)
    {
        $result =$this->ajax_pdd_publish_stat($item);

        return $result;
    }

    protected function jdstatus($status)
    {
        switch ($status) {
case 16:
$text = '已付款';
break;
case 17:
$text = '已完成';
break;
case 15:
$text = '待付款';
break;
case 1:
$text = '无效订单';
break;
case 24:
$text = '已付定金';
break;
default:
$text = '';
}

        return $text;
    }

    protected function pddstatus($status)
    {
        switch ($status) {
case 0:
$text = '已支付';
break;
case 1:
$text = '已成团';
break;
case 2:
$text = '已收货';
break;
case 3:
$text = '审核通过';
break;
case 4:
$text = '审核失败';
break;
case 5:
$text = '已结算';
break;
default:
$text = '';
}

        return $text;
    }

    protected function ajax_pdd_publish_stat($item)
    {
        if ($item['uid'] && is_numeric($item['uid'])) {
            $field = 'id,
(select status from tqk_pddorder where order_sn="'.$item['order_sn'].'" AND goods_id="'.$item['goods_id'].'" AND order_amount ="'.$item['order_amount'].'" limit 1) as oid,
(select id from tqk_pddorder where order_sn="'.$item['order_sn'].'" AND goods_id="'.$item['goods_id'].'" AND order_amount ="'.$item['order_amount'].'" limit 1) as poid,
(select fuid from tqk_user where id = '.$item['uid'].') as fuid,
(select guid from tqk_user where id = '.$item['uid'].') as guid,
(select webmaster_rate from tqk_user where id = '.$item['uid'].') as rate';
        } else {
            $field = 'id,
(select status from tqk_pddorder where order_sn="'.$item['order_sn'].'" AND goods_id="'.$item['goods_id'].'" AND order_amount ="'.$item['order_amount'].'" limit 1) as oid,
(select id from tqk_pddorder where order_sn="'.$item['order_sn'].'" AND goods_id="'.$item['goods_id'].'" AND order_amount ="'.$item['order_amount'].'" limit 1) as poid';
        }
        $admod = D('admin');
        $res=$admod->field($field)->order('id desc')->limit(1)->find();
        $item['fuid'] = $res['fuid'];
        $item['guid'] = $res['guid'];
        $item['leve1'] = $res['rate'] ? $res['rate'] : $item['leve1'];
        $item['uid'] = is_numeric($item['uid']) ? $item['uid'] : 0;

        if (!$res['poid'] && $item['status']>=0) {
            $mod = D('pddorder');
            $mod->create($item);
            $item_id = $mod->add();
            if ($item_id) {
				if($item['uid']>0){
				$wdata = array(
						'url'=>'c=duoduo&a=duoduoorder',
						'uid'=>$item['uid'],
						'keyword1'=>$item['order_sn'],
						'keyword2'=>$item['goods_name'],
						'keyword3'=>$item['order_amount'],
						'keyword4'=>$item['promotion_amount']*($item['leve1']/100)
						);
						Weixin::orderTaking($wdata);
				}
                return 1;
            }
            return 0;
        } elseif ($res['poid'] && $res['oid']!=$item['status'] && $item['status']>=0) {
            $mod = D('pddorder');
            $data = [
                'status'=>$item['status'],
                'order_status'=>$item['order_status'],
                'order_settle_time'=>$item['status']==5 ? $item['order_settle_time'] : ''
            ];
            $where= [
                'order_sn'=>$item['order_sn'],
                'goods_id'=>$item['goods_id'],
                'order_amount'=>$item['order_amount']
            ];
            $res = $mod->where($where)->save($data);

            if ($res) {
                return 1;
            }
            return 0;
        }
        return 0;
    }

    /**
     * 生成宣传海报
     * @param  array 	参数,包括图片和文字
     * @param  string 	$filename 生成海报文件名,不传此参数则不生成文件,直接输出图片
     * @param mixed $config
     * @return [type] [description]
     */
    protected function createPoster($config=[], $filename="")
    {
        //如果要看报什么错，可以先注释调这个header
        //if(empty($filename)) header("content-type: image/png");
        $imageDefault = [
            'left'=>0,
            'top'=>0,
            'right'=>0,
            'bottom'=>0,
            'width'=>100,
            'height'=>100,
            'opacity'=>100
        ];
        $textDefault =  [
            'text'=>'',
            'left'=>0,
            'top'=>0,
            'fontSize'=>32,             //字号
            'fontColor'=>'255,255,255', //字体颜色
            'angle'=>0,
        ];

        $background = $config['background'];//海报最底层得背景
        //背景方法
        $backgroundInfo = @getimagesize($background);
        $backgroundFun = 'imagecreatefrom'.image_type_to_extension($backgroundInfo[2], false);
        $background = $backgroundFun($background);
        $backgroundWidth = imagesx($background);    //背景宽度
    $backgroundHeight = imagesy($background);   //背景高度

    $imageRes = imageCreatetruecolor($backgroundWidth, $backgroundHeight);
        $color = imagecolorallocate($imageRes, 0, 0, 0);
        imagefill($imageRes, 0, 0, $color);

        // imageColorTransparent($imageRes, $color);    //颜色透明

        imagecopyresampled($imageRes, $background, 0, 0, 0, 0, imagesx($background), imagesy($background), imagesx($background), imagesy($background));

        //处理了图片
        if (!empty($config['image'])) {
            foreach ($config['image'] as $key => $val) {
                $val = array_merge($imageDefault, $val);
                $info = @getimagesize($val['url']);
                $function = 'imagecreatefrom'.image_type_to_extension($info[2], false);
                if ($val['stream']) {		//如果传的是字符串图像流
                    $info = getimagesizefromstring($val['url']);
                    $function = 'imagecreatefromstring';
                }
                $res = $function($val['url']);
                $resWidth = $info[0];
                $resHeight = $info[1];
                //建立画板 ，缩放图片至指定尺寸
                $canvas=imagecreatetruecolor($val['width'], $val['height']);
                imagefill($canvas, 0, 0, $color);
                //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
                imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'], $resWidth, $resHeight);
                $val['left'] = $val['left']<0 ? $backgroundWidth- abs($val['left']) - $val['width'] : $val['left'];
                $val['top'] = $val['top']<0 ? $backgroundHeight- abs($val['top']) - $val['height'] : $val['top'];
                //放置图像
            imagecopymerge($imageRes, $canvas, $val['left'], $val['top'], $val['right'], $val['bottom'], $val['width'], $val['height'], $val['opacity']);//左，上，右，下，宽度，高度，透明度
            }
        }

        //处理文字
        if (!empty($config['text'])) {
            foreach ($config['text'] as $key => $val) {
                $val = array_merge($textDefault, $val);
                list($R, $G, $B) = explode(',', $val['fontColor']);
                $fontColor = imagecolorallocate($imageRes, $R, $G, $B);
                $val['left'] = $val['left']<0 ? $backgroundWidth- abs($val['left']) : $val['left'];
                $val['top'] = $val['top']<0 ? $backgroundHeight- abs($val['top']) : $val['top'];
                imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $fontColor, $val['fontPath'], $val['text']);
            }
        }

        //生成图片
        if (!empty($filename)) {
            $res = imagejpeg($imageRes, $filename, 90); //保存到本地
            imagedestroy($imageRes);
            if (!$res) {
                return false;
            }
            return $filename;
        }
        imagejpeg($imageRes);			//在浏览器上显示
        imagedestroy($imageRes);
    }
	
	
	protected function ajax_duomai_publish_stat($item)
	    {
			
		 $mod = D('dmorder');
		 if($item['uid'] && is_numeric($item['uid']) && $item['uid']!='m001') {
		 $res = M('user')->field('id,fuid,guid,webmaster_rate')->where(array('id'=>$item['uid']))->find();
		 $item['fuid'] = $res['fuid'];
		 $item['guid'] = $res['guid'];
		 $item['leve1'] = $res['webmaster_rate'] ? $res['webmaster_rate'] : $item['leve1'];
		 $item['uid'] = $item['uid'];
		 
		 }
		 $result = $mod->create($item);
		 if (!$result){
			 $mod->setError(); //解决遇到错误无法循环，注意位置
			 $data = array(
			'goods_name'=>$item['goods_name'],
			 'status'=>$item['status'],
			 'order_status'=>$item['order_status'],
			 'orders_price'=>$item['confirm_price']>0?$item['confirm_price']:$item['orders_price'],
			 'order_commission'=>$item['confirm_siter_commission']>0?$item['confirm_siter_commission']:$item['order_commission'],
			 'charge_time'=>$item['charge_time']
			 );
			 $res = $mod->where(array('order_sn'=>$item['order_sn']))->save($data);
			 if ($res) {
			     return 1;
			 }
			
			 
		 }else{
			$item_id = $mod->add();
			if ($item_id) {
				if($item['uid']>0){
				$wdata = array(
						'url'=>'c=third&a=order',
						'uid'=>$item['uid'],
						'keyword1'=>$item['order_sn'],
						'keyword2'=>$item['goods_name'],
						'keyword3'=>$item['orders_price'],
						'keyword4'=>$item['order_commission']*($item['leve1']/100)
						);
						Weixin::orderTaking($wdata);
				}
			     return 2;
			 }
			 
			 
		 }
		
		 return 0;
	
	     
	    }
	
	
	protected function ajax_meituan_publish_stat($item)
	    {
			
		 $mod = D('mtorder');
		 if($item['sid'] && is_numeric($item['sid']) && $item['sid']!='m001') {
		 $res = M('user')->field('id,fuid,guid,webmaster_rate')->where(array('id'=>$item['sid']))->find();
		 $item['fuid'] = $res['fuid'];
		 $item['guid'] = $res['guid'];
		 $item['leve1'] = $res['webmaster_rate'] ? $res['webmaster_rate'] : $item['leve1'];
		 $item['uid'] = $item['sid'];
		 
		 }
		 $result = $mod->create($item);
		 if (!$result){
			 $mod->setError(); //解决遇到错误无法循环，注意位置
			 $data = array(
			 'status'=>$item['status'],
			 'settle_time'=>$item['status']==8?time():''
			 );
			 $res = $mod->where(array('orderid'=>$item['orderid']))->save($data);
			 if ($res) {
			     return 1;
			 }
			
			 
		 }else{
			$item_id = $mod->add();
			if ($item_id) {
				if($item['uid']>0){
				$wdata = array(
						'url'=>'c=meituan&a=order',
						'uid'=>$item['uid'],
						'keyword1'=>$item['orderid'],
						'keyword2'=>$item['smstitle'],
						'keyword3'=>$item['payprice'],
						'keyword4'=>$item['profit']*($item['leve1']/100)
						);
						Weixin::orderTaking($wdata);
				}
			     return 1;
			 }
			 
			 
		 }
		
		 return 0;
	
	     
	    }
	

    protected function ajax_jd_publish_stat($item)
    {
        if ($item['positionId'] && is_numeric($item['positionId'])) {
            $field = 'id,
(select oid from tqk_jdorder where oid="'.$item['oid'].'" limit 1) as oid,
(select id from tqk_user where jd_pid = '.$item['positionId'].') as uid,
(select fuid from tqk_user where jd_pid = '.$item['positionId'].') as fuid,
(select guid from tqk_user where jd_pid = '.$item['positionId'].') as guid,
(select webmaster_rate from tqk_user where jd_pid = '.$item['positionId'].') as rate';
        } else {
            $field = 'id,
(select oid from tqk_jdorder where oid="'.$item['oid'].'" limit 1) as oid';
        }
        $admod = D('admin');
        $res=$admod->field($field)->order('id desc')->limit(1)->find();
        $item['fuid'] = $res['fuid'];
        $item['guid'] = $res['guid'];
        $item['leve1'] = $res['rate'] ? $res['rate'] : $item['leve1'];
        $item['uid'] = $res['uid'];

        if ($item['validCode']<15 || ($item['validCode']>17 && $item['validCode']<24)) {
            $item['validCode'] = 1;
        }

        if (!$res['oid']) {
            $mod = D('jdorder');
            $mod->create($item);
            $item_id = $mod->add();
            if ($item_id) {
				if($item['uid']>0){
				$wdata = array(
						'url'=>'c=jindong&a=jdorder',
						'uid'=>$item['uid'],
						'keyword1'=>$item['orderId'],
						'keyword2'=>$item['skuName'],
						'keyword3'=>$item['estimateCosPrice'],
						'keyword4'=>$item['estimateFee']*($item['leve1']/100)
						);
						Weixin::orderTaking($wdata);
				}
                return 1;
            }
            return 0;
        } elseif ($res['oid']) {
            $mod = D('jdorder');
            $where= [
                'oid'=>$item['oid']
            ];
            unset($item['leve1'], $item['fuid'], $item['guid'], $item['leve2'], $item['leve3'], $item['addTime']);

            $res = $mod->where($where)->save($item);

            if ($res) {
                return 1;
            }
            return 0;
        }
        return 0;
    }
}
