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

    /**
     * @return void
     */
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
		
		if(C('yh_openjd') == 1){
			$this->assign('openjd',ture);
		}

        if (C('yh_openduoduo')) {
			
			$PddData = S('pddwapdata');
			if(!$PddData){
				$data = $this->PddGoodsSearch('','','','','4',10,true);
				S('pddwapdata',$data['goodslist']);
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


    if(	false === $Topsell = S('topsell')){
        $apiurl = $this->tqkapi . '/Topsell';
        $apidata = [
            'tqk_uid' => $this->tqkuid,
            'time' => time(),
            'size'=>40
        ];
        $token = $this->create_token(trim(C('yh_gongju')), $apidata);
        $apidata['token'] = $token;
        $res = $this->_curl($apiurl, $apidata, false);
        $goodslist = json_decode($res,true);
        $Topsell = $goodslist['data'];
        S('topsell',$Topsell,3600);
    }

        $this->assign('list', $Topsell);

        $modarticle=new articleModel();
        $article_list =$modarticle->where('ordid>0 and status=1')
->order('ordid asc,id desc')
->field('title,cate_id,add_time,id,pic,info,url,urlid')
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
                    $goodslist[$k]['linkurl']=U('article/read', ['id'=>$v['urlid']]);
                }
            }
            $this->assign('articlelist', $goodslist);
        }


        $this->assign('subNav',$this->SubNav());

        $this->_config_seo(C('yh_seo_config.index'));

            $this->display();

    }

    /**
     * @return array|array[]
     */
    protected function SubNav(){

        $data = [
            array(
                'name' => '签到红包',
                'tip' => '省',
                'link' => U('signin/index'),
                'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01PCcjdW2MgYsptBPXb_!!3175549857.png'
            )
        ];
        if(C('yh_openjd') == 1) {
            $jd = array(
                array(
                    'name' => '话费充值',
                    'tip' => '特惠',
                    'link' => U('recharge/index'),
                    'img' => 'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01NNZXb62MgYkpHmTRi_!!3175549857.png'
                ),
                array(
                    'name' => '京东',
                    'tip' => '',
                    'link' => U('jd/index'),
                    'img' => 'https://img.alicdn.com/imgextra/i4/3175549857/O1CN018G4PHE2MgYl27Wi9W_!!3175549857.png'
                )
            );
            $data = array_merge($jd,$data);
        }

        if(C('yh_openduoduo') == 1) {
            $duoduo = array(
                array(
                    'name' => '拼多多',
                    'tip' => '',
                    'link' => U('pdd/index'),
                    'img' => 'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01XaqX9a2MgYl0G5RIk_!!3175549857.png'
                )
            );
            $data = array_merge($duoduo,$data);
        }



        if(C('yh_dm_cid_qz') == 1) {
            $qz = array(
                array(
                    'name' => '特惠电影票',
                    'tip' => '',
                    'link' => U('elm/other',array('id'=>'6680','type'=>1)),
                    'img' => 'https://img.alicdn.com/imgextra/i4/3175549857/O1CN012RSNtR2MgYl0bzAJP_!!3175549857.png'
                )
            );
            $data = array_merge($qz,$data);
        }
        if(C('yh_dm_cid_dd') == 1) {
            $didi = array(
                array(
                    'name' => '滴滴打车券',
                    'tip' => '',
                    'link' => U('didi/index'),
                    'img' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01EXx3Df2MgYl2PEl7y_!!3175549857.png'
                ),
                array(
                    'name' => '汽车加油券',
                    'tip' => '',
                    'link' => U('didi/index',array('tab'=>1)),
                    'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01p7y0ck2MgYl2aiDUn_!!3175549857.png'
                ),
            );
            $data = array_merge($didi,$data);
        }
        if(C('yh_elm') == 1) {
            $elm = array(
                array(
                    'name' => '饿了么红包',
                    'tip' => '',
                    'link' => U('elm/index'),
                    'img' => '/Public/skin2/static/home/images/elm-4.png'
                )
            );
            $data = array_merge($elm,$data);
        }
        if(C('yh_openmt') == 1 || C('yh_dm_cid_mt') == 1) {
            $mt = array(
                array(
                    'name' => '美团外卖券',
                    'tip' => '',
                    'link' => U('takeout/index'),
                    'img' => 'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01MgRSKf2MgYktTWx1Q_!!3175549857.png'
                )
            );
            $data = array_merge($mt,$data);
        }
        if(C('yh_dm_cid_kfc') == 1) {
            $kfc = array(
                array(
                    'name' => '肯德基',
                    'tip' => '5折起',
                    'link' => U('elm/other', array('id' => '5933', 'type' => 1)),
                    'img' => 'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01vv0lrn2MgYl2HB95o_!!3175549857.png'
                )
            );
            $data = array_merge($kfc,$data);
        }
        if(C('yh_vphpid')) {
            $vph = array(
                array(
                    'name' => '唯品会',
                    'tip' => '',
                    'link' => U('vph/index'),
                    'img' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN018T0JF82MgYrv4Lu75_!!3175549857.png'
                )
            );
            $data = array_merge($vph,$data);
        }
        if(C('yh_dm_cid_dy') == 1) {
            $douyin = array(
                array(
                    'name' => '抖音精选',
                    'tip' => '返',
                    'link' => U('douyin/index'),
                    'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01DImHGL2MgYktgKkCY_!!3175549857.png'
                )
            );
            $data = array_merge($douyin,$data);
        }
        if(C('yh_dm_cid_dd') == 1) {
            $hua = array(
                array(
                    'name' => '花小猪红包',
                    'tip' => '',
                    'link' => U('didi/index',array('tab'=>3)),
                    'img' => 'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01pt6DRw2MgYseqGjl4_!!3175549857.png'
                ),
                array(
                    'name' => '滴滴货运券',
                    'tip' => '',
                    'link' => U('didi/index',array('tab'=>2)),
                    'img' => 'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01iwyIon2MgYslNxmaQ_!!3175549857.png'
                )
            );
            $data = array_merge($hua,$data);
        }
        if(C('yh_dm_cid_dy') == 1) {
            $douyin = array(
                array(
                    'name' => '抖音1分购',
                    'tip' => '',
                    'link' => U('douyin/fen'),
                    'img' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01AFTjxc2MgYgqhg8jk_!!3175549857.png'
                )
            );
            $data = array_merge($douyin,$data);
        }
        if(C('yh_taolijin') > 0) {
            $lijin = array(
                array(
                    'name' => '淘礼金专区',
                    'tip' => '',
                    'link' => U('topljin/index'),
                    'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01Tijl0c2MgYkyo5tKZ_!!3175549857.png'
                )
            );
            $data = array_merge($lijin,$data);
        }
            $other = array(
                array(
                    'name' => '品牌券',
                    'tip' => '',
                    'link' => U('brand/index'),
                    'img' => '/Public/static/wap/images/icon/cjmbq-3.png'
                ),
                array(
                    'name' => '百亿补贴',
                    'tip' => '',
                    'link' => U('special/index',array('id'=>4620)),
                    'img' => 'https://img.alicdn.com/imgextra/i4/126947653/O1CN01WEGWHb26P7kkTh09r_!!126947653.png'
                ),
                array(
                    'name' => '天猫超市',
                    'tip' => '',
                    'link' => U('special/index',array('id'=>3022)),
                    'img' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01TNF99w2MgYkpOR0ym_!!3175549857.png'
                ),
                array(
                    'name' => '超划算',
                    'tip' => '',
                    'link' => U('chaohuasuan/index'),
                    'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01vemkIN2MgYkrJyOCb_!!3175549857.png'
                ),
                array(
                    'name' => '超级U选',
                    'tip' => '补贴',
                    'link' => U('uxuan/index',array('id'=>4956)),
                    'img' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01LJdLke2MgYqLLxz7y_!!3175549857.png'
                ),
                array(
                    'name' => '海淘优选',
                    'tip' => '',
                    'link' => U('haitao/index'),
                    'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01IHtgDr2MgYl2aIT3v_!!3175549857.png'
                ),
            );
            $data = array_merge($other,$data);

            return array_reverse($data);

    }

    /**
     * @return void
     */
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
