<?php
namespace Home\Action;

use Common\Model\itemscateModel;
use Common\Model\itemsModel;
use Common\Model\jditemsModel;
use Common\Model\pdditemsModel;
use Common\Model\articleModel;
use Common\Model\brandModel;

class IndexAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $reurl=$_SERVER['REQUEST_URI'];
        $reurl=str_replace('index.php/', '', $reurl);
        if ($this->isMobile()) {
            echo('<script>window.location.href="'.C('yh_headerm_html').$reurl.'"</script>');
        }
        $this->_mod = D('items')->cache(true, 5 * 60);
        $this->_ad = D('ad')->cache(true, 10 * 60);
    }

    public function index()
    {
        $ItemMod = new itemsModel();
        $ItemCate = new itemscateModel();
        $this->assign('ItemCate', $ItemCate->cate_cache());
		$adCache = S('adcache');
		if(!$adCache){
        $adlist=$this->_ad->field('url,img,status')->where('beginTime<'.NOW_TIME.' and endTime>'.NOW_TIME.' and (status=2 or status = 0 or status = 7 or status = 8) and add_time=0')->order('ordid asc,id desc')->select();
        if ($adlist) {
            $ad=[];
            $topad=[];
            $rightad=[];
			$downad = [];
            foreach ($adlist as $k=>$v) {
                if ($v['status']==0) {
                    $ad[$k]['url']=$v['url'];
                    $ad[$k]['img']=$v['img'];
                } elseif ($v['status']==2) {
                    $topad['url']=$v['url'];
                    $topad['img']=$v['img'];
                } elseif ($v['status']==7) {
                    $rightad['url']=$v['url'];
                    $rightad['img']=$v['img'];
				} elseif ($v['status']==8) {
				    $downad[$k]['url']=$v['url'];
				    $downad[$k]['img']=$v['img'];
				}
            }
	$cacheData = array(
	  'topad'=>$topad,
	  'rightad'=>$rightad,
	  'ad'=>array_values($ad),
	  'downad'=>array_values($downad)
	);
		S('adcache',$cacheData);

            $this->assign('ad_list', ['topad'=>$topad, 'rightad'=>$rightad, 'bannerad'=>array_values($ad),'downad'=>array_values($downad)]);
        }
		
		}else{
			
		 $this->assign('ad_list', ['topad'=>$adCache['topad'], 'rightad'=>$adCache['rightad'], 'bannerad'=>$adCache['ad'],'downad'=>$adCache['downad']]);
			
		}

        $ArticleMod = new articleModel();
        $toutiao = $ArticleMod->articleList(10);
        if ($toutiao) {
            $this->assign('toutiao', $toutiao);
        } else {
            $NewGoods = $ItemMod->GoodsList(10, ['pass'=>1, 'isshow'=>1, 'ems'=>1], 'ordid desc');
            $this->assign('newgoods', $NewGoods);
        }

 if (C('yh_openjd')) {
        $Jdmod = new jditemsModel();

        $where = [
            'comments' => ['gt', 10000],
            'quan'=>['gt', 0]
        ];
        $order = 'id desc';
        $jditem = $Jdmod->Jditems(30, $where, $order);
        $this->assign('jdlist', $jditem);
}

        //$WelfareGoods = $ItemMod->GoodsList(3,array('pass'=>1,'isshow'=>1),'ems desc,is_commend desc');
        // $sql="(select `id`,`pic_url`,`title`,`num_iid`,`coupon_price`,`price`,`quan`,`shop_type`,`volume`,`add_time`,`commission_rate` from tqk_items  WHERE ems = 2  AND `pass` = 1 AND `isshow` = 1 AND `status` = 'underway'  ORDER BY id DESC limit 0,3) union (select `id`,`pic_url`,`title`,`num_iid`,`coupon_price`,`price`,`quan`,`shop_type`,`volume`,`add_time`,`commission_rate` from tqk_items where `pass` = 1 AND `isshow` = 1 AND `status` = 'underway'  ORDER BY `is_commend` desc limit 0,3 )";
        // $WelfareGoods = M()->query($sql);
        // $this->assign('WelfareGoods', $WelfareGoods);

        $where = [
            'pass'=>1,
            'isshow'=>1,
            'volume'=>['gt', 5000],
            'coupon_price'=>['lt', 20]
        ];

        $sqlwhere = ['cate_id'=>28026, 'volume'=>['gt', 10]];
        $haoquan = $ItemMod->GoodsList(5, $sqlwhere, 'id desc');
        $this->assign('haoquan', $haoquan);

        $pagesize = C('yh_index_page_size') ? C('yh_index_page_size') : 20;
        $where=[
            'pass'=>1,
            'isshow'=>1,
            'ems'=>1,
            'cate_id'=>['lt', 18]
        ];
        if (C('yh_index_shop_type')) {
            $where['shop_type']=C('yh_index_shop_type');
        }
        $where['quan']=[[
            'egt',
            C('yh_index_mix_price')
        ],
        [
            'elt',
            C('yh_index_max_price')
        ]];
        $where['volume']=[[
            'egt',
            C('yh_index_mix_volume')
        ],
        [
            'elt',
            C('yh_index_max_volume')
        ]];

        $recommend = $ItemMod->GoodsList(30, $where, C('yh_index_sort'));
        $this->assign('recommend', $recommend);

        $BrandMod = new brandModel();
        $brandlist = $BrandMod ->BrandList(30, 'ordid desc');
        $this->assign('brandlist', $brandlist);

        //$bestseller = $this->TbkDgMaterial(20,3786);

        $bestseller = $ItemMod->GoodsList(13, ['quan'=>['gt', 100], 'coupon_price'=>['gt', 200]], 'id desc');
        $this->assign('bestseller', $bestseller);

        $beonsale = $ItemMod->GoodsList(10, ['cate_id'=>27160], 'id desc');
        $this->assign('beonsale', $beonsale);

        if (C('yh_openduoduo')) {
			$PddData = S('pddhomedata');
			if(!$PddData){
				$data = $this->PddGoodsSearch('','','','','4',30,true);
				S('pddhomedata',$data['goodslist']);
			}else{
				$data['goodslist'] = $PddData;
			}
			
            $this->assign('pdditems', $data['goodslist']);
        }

        $this->_config_seo(C('yh_seo_config.index'));
		
		$topic=M('topic')->cache(true, 10 * 60)->where('status = 1')->field('name,url')->order('id desc')->limit(30)->select();
		$this->assign('topic',$topic); 

        $link=M('link')->cache(true, 50 * 60)->field('name,url')->where('status=1')->order('ordid asc')->select();
        $this->assign('link', $link);
		
		 $this->assign('takeout',$this->Takeout());
        $this->display();
		
		 
		
    }
}
