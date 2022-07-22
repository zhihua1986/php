<?php
namespace Home\Action;
use Common\Model\userModel;
use Common\Model\adModel;
use Common\Model\itemscateModel;
use Common\Model\itemsModel;
use Common\Model\usercashModel;
use Common\Model\articleModel;
use Common\Model\helpModel;
use Common\Model\jdorderModel;
use Common\Model\pddorderModel;
use Common\Model\jditemsModel;
class AppletsAction extends BaseAction
{
	public function _initialize()
	{
	    parent::_initialize();
	    C('SESSION_AUTO_START',false);
		C('URL_ROUTER_ON',false);
		
	    $info= htmlspecialchars_decode(I('data'));
	    $info = json_decode($info, true);
	    if ($info) {
	        $this->params=$info;
	    } else {
	        $this->params=I('param.');
	    }
	    $this->onlytime=I('time', '0', 'number_int');
	    $this->accessKey = trim(C('yh_gongju'));
	    $this->checkTime($this->onlytime);
	    $this->checkKey();
		$this->version =$this->params['ver'];
		$this->cli=$this->params['cli'];
	}
	
protected function bdmenu(){
	
	$data = [
	    [
	        'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01tSsWzW2MgYktT3sbn_!!3175549857.png',
	        'name'=>'9块9包邮',
	        'url'=>'/pages/other/toplist?id=jiu&name=9块9包邮',
	        'type'=>1,
	    ],
		[
		    'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01oyiNEj2MgYhkOug8N_!!3175549857.png',
		    'name'=>'热门推荐',
		    'url'=> '/pages/other/toplist?id=tuijian&name=热门推荐',
		    'type'=>1,
		],
		// [
		//     'img'=>'https://img.alicdn.com/imgextra/i2/126947653/O1CN01zlA1SS26P7nz7o69l_!!126947653.gif',
		//     'name'=>'领超级红包',
		//     'url'=>'/pages/other/topic?id=20150318020007201',
		//     'type'=>1,
		// ],
	    [
	        'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01vemkIN2MgYkrJyOCb_!!3175549857.png',
	        'name'=>'超划算',
	        'url'=>'/pages/other/toplist?id=chaohuasuan&name=超划算',
	        'type'=>1,
	    ],
	    [
	        'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01BPGIkm2MgYkwjSWQT_!!3175549857.png',
	        'name'=>'1分包邮',
	        'url'=>'/pages/other/topic?id=20150318020008177',
	        'type'=>1,
	    ],
	    [
	        'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01LQBd6P2MgYkxCtEb7_!!3175549857.png',
	        'name'=>'新手指南',
	        'url'=>'/pages/article/help',
	        'type'=>1,
	    ]
	];
	
	return $data;
	
}

	protected function localTab(){
		
		$data=array(
		array(
		'name'=>'全部活动',
		'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN0193HGnv2MgYhCVjpYy_!!3175549857.png',
		'id'=>'all'
		),
		array(
		'name'=>'饿了么红包',
		'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01NusLwu2MgYhMMsZ8E_!!3175549857.png',
		'id'=>'elm',
		),
		array(
		'name'=>'优惠打车',
		'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01YHf5Zn2MgYkf7uZ5w_!!3175549857.png',
		'id'=>'dc',
		),
		array(
		'name'=>'电影票',
		'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01GrvGwT2MgYkaeikhW_!!3175549857.png',
		'id'=>'dyp',
		),
		array(
		'name'=>'口碑生活',
		'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01qq2XbJ2MgYeTtiemQ_!!3175549857.png',
		'id'=>'kb',
		)
		
		);
		
		if(C('yh_openmt') == '1'){
		 $meituan = array(
		 array(
		'name'=>'美团红包',
		'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01v7HDtL2MgYhOcDcvh_!!3175549857.png',
		'id'=>'mt',
		)
		);
		$data = array_merge($data,$meituan);
		}
		if(C('yh_openduoduo') == '1'){
		$chongzhi = array(
		array(
		'name'=>'话费充值',
		'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01s3ISxW2MgYhNyid2T_!!3175549857.png',
		'id'=>'cz',
		)
		);
		$data = array_merge($data,$chongzhi);
		}
		
		return $data;
	}
	
	protected function localContent($tab){
		
	   $mt = array(
		array(
		'name'=>'美团外卖红包天天领',
		'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01OIOMJ02MgYhQvfKXk_!!3175549857.jpg',
		'id'=>'33',
		'tab'=>'mt'
		),
		array(
		'name'=>'美团商超红包天天领',
		'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01uHgOl92MgYhPtcnTD_!!3175549857.jpg',
		'id'=>'4',
		'tab'=>'mt'
		),
		array(
		'name'=>'美团优选 特价菜场 便宜有好货',
		'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01FL4RCB2MgYhxgRXBz_!!3175549857.png',
		'id'=>'105',
		'tab'=>'mt'
		)
		);
		
	  $cz = array(
				array(
				'name'=>'话费充值',
				'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01S6ZVoa2MgYhPPzvp4_!!3175549857.jpg',
				'id'=>'chong',
				'tab'=>'cz'
				)
				);
	  $elm = array(
	  array(
	  'name'=>'饿了么外卖红包',
	  'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01zsJKeN2MgYhJugg0j_!!3175549857.jpg',
	  'id'=>'8877',
	  'tab'=>'elm'
	  ),
	  array(
	  'name'=>'饿了么果蔬商超红包',
	  'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01jW5rMB2MgYhPKYqZh_!!3175549857.jpg',
	  'id'=>'4441',
	 'tab'=>'elm'
	  ),
	  );
	  
	  $dc = array(
	  				array(
	  				'name'=>'高德打车',
	  				'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01iiNSAc2MgYkfGJWWA_!!3175549857.jpg',
	  				'id'=>'gaode',
	  				'tab'=>'dc'
	  				)
	  		);
	$dyp = array(
					array(
					'name'=>'淘票票',
					'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01qeGnZN2MgYkfMi9g3_!!3175549857.jpg',
					'id'=>'taopiaopiao',
					'tab'=>'dyp'
					)
			);
	  
	  $kb = array(
	  				array(
	  				'name'=>'口碑生活大牌美食5折起',
	  				'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01pjhoUr2MgYhSPuRtQ_!!3175549857.jpg',
	  				'id'=>'1583739244161',
					'tab'=>'kb'
	  				)
	  		);
		
		if($tab == 'all'){
			
			$data = array_merge($dc,$dyp,$elm);
			if(C('yh_openmt') == '1'){
			$data = array_merge($mt,$data);
			}
			if(C('yh_openduoduo') == '1'){
			$data = array_merge($cz,$data);
			}
			
			$data = array_merge($data,$kb);
			
			return $data;
			
		}else{
			
			return $$tab;
			
		}
		
		return false;
		
	}
	
    protected function submenu()
    {
	
	if($this->cli == 'bd'){
		
		$data = $this->bdmenu();
	    return $data;
	}	
	
	  $part1 = [
		[
		    'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN018G4PHE2MgYl27Wi9W_!!3175549857.png',
		    'name'=>'京东',
		    'url'=>'/pages/cate/multi?mod=jd',
		    'type'=>1,
		]  
		  
	  ];
	  if (C('yh_openduoduo')){
	  	$part1[] =  [
	          'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01XaqX9a2MgYl0G5RIk_!!3175549857.png',
	          'name'=>'拼多多',
	          'url'=>'/pages/cate/multi?mod=pdd',
	          'type'=>1,
	      ];
	  }
	
	 $part2 = [];
	 if(C('yh_dm_cid_mt') == 1){
	 	$part2[] =  [
	 	    'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01MgRSKf2MgYktTWx1Q_!!3175549857.png',
	 	    'name'=>'美团外卖',
			'url'=> '/pages/other/share?id=1&tab=dmmt&cur=0',
			'id'=>'1',
			'type'=>1,
	 	];
	 }elseif(C('yh_openmt') == 1){
	 	
	 	$part2[] =  [
	 	    'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01MgRSKf2MgYktTWx1Q_!!3175549857.png',
	 	    'name'=>'美团外卖',
	 	   'url'=> '/pages/other/share?id=33&tab=mt&cur=0',
	 	   'id'=>'33',
	 	   'type'=>1,
	 	];
	 	
	 }
	 if(C('yh_dm_cid_kfc') == 1){
		 $part2[] =  [
		   'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01vv0lrn2MgYl2HB95o_!!3175549857.png',
		   'name'=>'肯德基',
		   'url'=>'/pages/index/index?src=tqk&cid=kfc',
		   'type'=>3,
		 ];
	 }
	if(C('yh_dm_cid_dd') == 1){
			 $part2[] =  [
			  'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01EXx3Df2MgYl2PEl7y_!!3175549857.png',
			  'name'=>'滴滴打车',
			  'url'=>'/pages/other/share?id=12485&tab=didi&cur=0',
			  'id'=>'12485',
			  'type'=>1,
			 ];
			 
			 $part2[] =  [
			  'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01p7y0ck2MgYl2aiDUn_!!3175549857.png',
			  'name'=>'汽车加油',
			  'url'=>'/pages/other/share?id=15200&tab=didi&cur=1',
			  'id'=>'15200',
			  'type'=>1,
			 ];
	}
	if(C('yh_dm_cid_qz') == 1){
			 $part2[] =  [
			'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN012RSNtR2MgYl0bzAJP_!!3175549857.png',
			'name'=>'特惠电影票',
			'url'=>'/pages/index/index?src=tqk&cid=qz',
			'type'=>3,
			 ];
	}
        $part3 = [
			[
			    'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01lH6WbT2MgYkx01LPz_!!3175549857.png',
			    'name'=>'高德打车',
			    'url'=>'gaode',
			    'type'=>2,
			],
			[
			    'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01vyZmbD2MgYkvpxc0R_!!3175549857.png',
			    'name'=>'淘票票',
			    'url'=>'taopiaopiao',
			    'type'=>2,
			],
            [
                'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01FRzfOs2MgYkyiAOiu_!!3175549857.png',
                'name'=>'饿了么',
                'url'=> '/pages/other/share?id=2192&tab=elm&cur=0',
				'id'=>'2192',
                'type'=>1,
            ],
            [
                'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN0185ADWl2MgYl1MitES_!!3175549857.gif',
                'name'=>'百亿补贴',
                'url'=>'/pages/other/topic?id=20150318020000462',
                'type'=>1,
            ],
			[
			    'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01BPGIkm2MgYkwjSWQT_!!3175549857.png',
			    'name'=>'1分包邮',
			    'url'=>'/pages/other/topic?id=20150318020008177',
			    'type'=>1,
			],
            [
                'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01vemkIN2MgYkrJyOCb_!!3175549857.png',
                'name'=>'超划算',
                'url'=>'/pages/other/toplist?id=chaohuasuan&name=超划算',
                'type'=>1,
            ],
            [
                'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01tSsWzW2MgYktT3sbn_!!3175549857.png',
                'name'=>'9块9包邮',
                'url'=>'/pages/other/toplist?id=jiu&name=9块9包邮',
                'type'=>1,
            ],
            [
                'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01NNZXb62MgYkpHmTRi_!!3175549857.png',
                'name'=>'话费充值',
                'url'=>'cz',
			   'id'=>'chong',
			   'type'=>2,
            ],
			[
			    'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01IHtgDr2MgYl2aIT3v_!!3175549857.png',
			    'name'=>'海淘优选',
			    'url'=>'/pages/other/toplist?id=haitao&name=海淘优选',
			    'type'=>1,
			],
            
        ];
		
		if(C('yh_dm_cid_dy') == 1){
				 $part3[] =  [
				 'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01DImHGL2MgYktgKkCY_!!3175549857.png',
				 'name'=>'抖音直播',
				 'url'=>'/pages/other/live',
				 'type'=>1,
				 ];
		}
		
		if(C('yh_taolijin') > 0){
				 $part3[] =  [
				 'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01Tijl0c2MgYkyo5tKZ_!!3175549857.png',
				 'name'=>'礼金专区',
				 'url'=>'/pages/other/toplijin',
				 'type'=>1,
				 ];
		}
		
		$part4 = [
			[
			    'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01TNF99w2MgYkpOR0ym_!!3175549857.png',
			    'name'=>'天猫超市',
			    'url'=>'/pages/other/topic?id=20150318020003022',
			    'type'=>1,
			],
			[
			    'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01LQBd6P2MgYkxCtEb7_!!3175549857.png',
			    'name'=>'新手指南',
			    'url'=>'/pages/article/help',
			    'type'=>1,
			]
			
		];

		$data = array_merge($part1,$part2,$part3,$part4);

        return $data;
    }	
	
	protected function getuid($openid)
	    {
	        $uid = S($openid);
	        if ($uid) {
	            return $uid;
	        }
	        $mod = new userModel();
	        $uid = $mod->field('webmaster_pid,id,fuid,guid,webmaster,webmaster_rate,jd_pid')->where(['openid'=>$openid])->find();
	        if ($uid) {
	            S($openid, $uid, 60);
	            return $uid;
	        }
	
	        return false;
	    }
	
	protected function orderstatic($id){
			switch($id){
				case 0 :
				return '待处理';
				break;
				case 1 :
				return '已付款';
				break;
				case 2 :
				return '无效订单';
				break;
				case 3 :
				return '已收货';
				break;
				default : 
				return '订单失效';
				break;
			}
			
		}
		
		protected function checkKey()
		{
		    $json = [
		        'code'=>'400',
		        'msg'=>'token验证失败'
		    ];
		
		    $token = md5(I('time').$this->accessKey);
		    if (!$token || $token!=I('token')) {
		        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
		    }
		}
		
		protected function OrderTab(){
			
		$Nav[]['name'] = '淘宝';
		$Action[]['action'] = 'userorder';
		$team[]['tmaction'] = 'teamorder';
		if(C('yh_openjd') == 1){
		$Nav[]['name']='京东';
		$Action[]['action']='jdorder';
		$team[]['tmaction'] = 'jdteam';
		}
		if(C('yh_openduoduo') == 1){
		$Nav[]['name']='拼多多';
		$Action[]['action']='pddorder';
		$team[]['tmaction'] = 'pddteam';
		}
		if(C('yh_openmt') == 1){
		$Nav[]['name']='美团';
		$Action[]['action']='mtorder';
		$team[]['tmaction'] = 'mtteam';
		}
		if(C('yh_dmappkey') > 1){
		$Nav[]['name']='其它';
		$Action[]['action']='dmorder';
		$team[]['tmaction'] = 'dmteam';
		}
		$data = array(
		'val'=>$Nav,
		'action'=>$Action,
		'tmaction'=>$team
		);
		return $data;
			
		}
		
		protected function Isopen(){
			$open = 0;
			$bd_version = C('yh_bd_version');
			$wx_version = C('yh_wx_version');
			switch($this->cli){
				
				case 'bd':
				if($bd_version == $this->version){
				$open = 1;	
				}
				
				if($bd_version == '2.0.0'){
				$open = 1;	
				}
				
				if($bd_version == '1.0.0'){
				$open = 0;	
				}
				
				break;
				
				case 'wx':
				if($wx_version == $this->version){
				$open = 1;	
				}
				
				if($wx_version == '2.0.0'){
				$open = 1;	
				}
				
				if($wx_version == '1.0.0'){
				$open = 0;	
				}
				
				break;
				default:
				
				if($wx_version == '2.0.0'){
				$open = 1;	
				}
				
				if($bd_version == '2.0.0'){
				$open = 1;	
				}
				
			}
			
			
			return $open;
			
		}
		
		protected function getElmLink($uid=''){
			vendor("taobao.taobao");
			$pid = trim(C('yh_taobao_pid')); 
			 if($uid){
			 	$R = A("Records");
			 	$data= $R ->content($uid,$uid); 
			 	$pid = $data['pid'];
			 }
			 $apppid=explode('_', $pid);
			 $AdzoneId=$apppid[3];
			 $c = new \TopClient();
			 $appkey=trim(C('yh_taobao_appkey'));
			 $appsecret=trim(C('yh_taobao_appsecret'));
			 $c->appkey = $appkey;
			 $c->secretKey = $appsecret;
			 $req = new \TbkActivityInfoGetRequest();
			 $req->setActivityMaterialId("20150318020002192");
			 $req->setAdzoneId($AdzoneId);
			 $RelationId = $this->params['relationid'];
			 if ($RelationId && $RelationId!='null') {
			     $req->setRelationId("".$RelationId."");
			 }
			 $resp = $c->execute($req);
			 $resp = json_decode(json_encode($resp), true);
			 if ($resp['data']['wx_miniprogram_path']) {
			     $data = ['path'=>$resp['data']['wx_miniprogram_path']];
				 return $data;
			 }
			 
			 return false;
			 
		}
		
		protected function jditem($id,$cid){
			
			$mod = new jditemsModel();
			$result=$mod->field('cid2', true)->where(['itemid'=>$id])->find();
			if(!$result){
			$detail = $this->JdGoodsDetail($id);
			$result['commission_rate']=$detail['commissionShare']; 
			$result['quan']=$detail['couponAmount']>0?$detail['couponAmount']:0;
			$result['couponlink']=$detail['couponLink'];
			$result['pict']=$detail['picMain'];
			$result['itemurl']=$detail['materialUrl'];
			$result['coupon_price']=$detail['actualPrice'];
			$result['price']=$detail['originPrice'];
			$result['owner']=$detail['isOwner'];
			$result['comments']=$detail['comments'];
			$result['cate_id']=$detail['cid1'];
			$result['itemid']=$detail['skuId'];
			$result['title']=$detail['skuName'];
			$result['detailImages']=$detail['detailImages'];
			}
			
			if(!$result['detailImages']){
				$info = $this->JdGoodsQuery($id);
				$result['detailImages'] = explode(",", $info['data'][0]['detailImages']);
			}
			
			
			foreach($result['detailImages'] as $k=>$v){
				if(strpos($v,'//') === 0){
					$result['detailImages'][$k]='https:'.$v;
				}else{
					$result['detailImages'][$k]=$v;
				}
			}
			
			if($cid){
			 $where = [
			     'quan'=>['gt', 0],
				 'cate_id'=>$cid
			 ];
			 $order = 'id desc';
			 $jditem = $mod->Jditems(8, $where, $order);
			 }
			
			if ($result) {
				$data = [
				    'detail'=>$result,
				    'relation'=>$jditem
				];
				return $data;
			} 
			
			return false;
			
		}
		
		protected function Exitjson($code, $msg, $result=[], $lang=[])
		{
			$open = $this->Isopen();
		    $json=[
		        'code'=>$code,
		        'msg'=>$msg,
		        'result'=>$result,
		        'lang'=>$lang,
		        'isopen'=>$open,
		        'isfan'=>C('yh_isfanli'),
		        'bili1'=>C('yh_bili1'),
		        'bili2'=>C('yh_bili2'),
		        'afterlogin'=>C('yh_islogin'),
		        'isauth'=>C('yh_bingtaobao'),
				'openmt'=>C('yh_openmt'),
				'openjd'=>C('yh_openjd'),
				'opendd'=>C('yh_openduoduo'),
				'opendm'=>C('yh_dmappkey')>0?1:0,
		        'stime'=>NOW_TIME
		    ];
		    exit(json_encode($json, JSON_UNESCAPED_UNICODE));
		}
		
		
	
}