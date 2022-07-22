<?php
namespace M\Action;

use Common\Model\articleModel;

class IndexAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->_ad = D('ad')->cache(true, 10 * 60);
        $this->_mod = D('items')->cache(true, 5 * 60);
        C('DATA_CACHE_TIME', C('yh_site_cachetime'));
    }

    public function mytan()
    {
        $this->display();
    }

    public function mytannoscript()
    {
        $this->display();
    }

    public function index()
    {
        $ad = $this->_ad->where('beginTime<'.NOW_TIME.' and endTime>'.NOW_TIME.' and (status=1 or status = 6) and add_time=0')->order('ordid asc,id desc')->select();
        $adlist=[];
        foreach ($ad as $k=>$v) {
            $adlist[$v['status']][$k]['url']=$v['url'];
            $adlist[$v['status']][$k]['img']=$v['img'];
        }
        $this->assign('ad_list_1', $adlist[1]);
        $this->assign('ad_list_6', $adlist[6]);

        if (C('yh_openduoduo')) {
			
			$PddData = S('pddhomedata');
			if(!$PddData){
				$data = $this->PddGoodsSearch('','','','','24',10,true);
				S('pddhomedata',$data['goodslist']);
			}else{
				$data['goodslist'] = $PddData;
			}
            $this->assign('pdditems', $data['goodslist']);
        }

        $where=[
            'pass'=>1,
            'isshow'=>1,
            'ems'=>2,
            'status'=>'underway'
        ];
        $jingxuan=$this->_mod->where($where)->field('id,pic_url,title,num_iid,coupon_price,price,quan,shop_type,volume,add_time')->order('id desc')->limit(5)->select();
        $this->assign('jingxuan', $jingxuan);

        $sqlwhere['pass'] = '1';
        $sqlwhere['isshow'] = '1';
        $sqlwhere['ems'] = '1';
        $sqlwhere['status'] = 'underway';
        if (C('yh_index_shop_type')) {
            $sqlwhere['shop_type']=C('yh_index_shop_type');
        }
        $sqlwhere['quan']=[[
            'egt',
            C('yh_index_mix_price')
        ],
        [
            'elt',
            C('yh_index_max_price')
        ]];
        $sqlwhere['volume']=[[
            'egt',
            C('yh_index_mix_volume')
        ],
        [
            'elt',
            C('yh_index_max_volume')
        ]];

        $items_list = $this->_mod->where($sqlwhere)->field('id,pic_url,title,num_iid,coupon_price,commission_rate,price,quan,shop_type,volume,add_time')->order(C('yh_index_sort'))->limit(trim(C('yh_index_page_size')))->select();
        if ($items_list) {
            $today=date('Ymd');
            $goodslist=[];
            foreach ($items_list as $k=>$v) {
                $goodslist[$k]['id']=$v['id'];
                $goodslist[$k]['num_iid']=$v['num_iid'];
                $goodslist[$k]['pic_url']=$v['pic_url'];
                $goodslist[$k]['title']=$v['title'];
                $goodslist[$k]['commission_rate']=$v['commission_rate'];
                $goodslist[$k]['coupon_price']=$v['coupon_price'];
                $goodslist[$k]['price']=$v['price'];
                $goodslist[$k]['quan']=(int) ($v['quan']);
                $goodslist[$k]['shop_type']=$v['shop_type'];
                $goodslist[$k]['volume']=$v['volume'];
                if ($today==date('Ymd', $v['add_time'])) {
                    $goodslist[$k]['is_new']=1;
                } else {
                    $goodslist[$k]['is_new']=0;
                }
                if (C('APP_SUB_DOMAIN_DEPLOY')) {
                    $goodslist[$k]['linkurl']=U('/item/', ['id'=>$v['num_iid']]);
                } else {
                    $goodslist[$k]['linkurl']=U('item/index', ['id'=>$v['num_iid']]);
                }
            }
        }

        $this->assign('list', $goodslist);
        $modarticle=new articleModel();
        $article_list =$modarticle->where('ordid>0 and status=1')
->order('ordid asc,id desc')
->field('title,cate_id,add_time,id,pic,info,url')
->limit(4)
->select();
        if ($article_list) {
            $goodslist=[];
            foreach ($article_list as $k=>$v) {
                $goodslist[$k]['id']=$v['id'];
                $goodslist[$k]['pic']=$v['pic'];
                $goodslist[$k]['cateid']=$v['cate_id'];
                $goodslist[$k]['title']=$v['title'];
                $goodslist[$k]['add_time']=date('Y-m-d', $v['add_time']);
                $goodslist[$k]['infocontent']=cut_html_str($v['info'], 80);
                if (C('APP_SUB_DOMAIN_DEPLOY') && C('URL_MODEL') == 2) {
                    $goodslist[$k]['linkurl']=$v['url'];
                } else {
                    $goodslist[$k]['linkurl']=U('article/read', ['id'=>$v['id']]);
                }
            }
            $this->assign('articlelist', $goodslist);
        }

        $this->_config_seo(C('yh_seo_config.index'));

        $this->display();
    }

    public function cate()
    {
        $ad = $this->_ad->where(['status'=>'1'])->order('id desc')->select();
        $this->assign('ad_list', $ad);
        $cateinfo = $this->_cate_mod->where(['status'=>1])->select();
        $article_list =$this->_article->where('status=1')
            ->field('id,title')
            ->order('ordid asc,id desc')
            ->limit(6)
            ->select();
        $this->assign('article_list', $article_list);
        $this->_config_seo(C('yh_seo_config.index'));

        $this->display('list/index');
    }
}
