<?php
namespace Admin\Action;

vendor('PHPExcel.PHPExcel');
class JdorderAction extends BaseAction
{
    //  protected $list_relation = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->tqkuid=trim(C('yh_app_kehuduan'));
        $tkapi=trim(C('yh_api_line'));
        if (false ===$tkapi) {
            $this->tqkapi = 'http://api.tuiquanke.cn';
        } else {
            $this->tqkapi = $tkapi;
        }
        $this->assign('list_table', true);
        $this->_name = 'jdorder';
    }

    public function sync()
    {
        F('jdjiesuan', null);
        if (IS_AJAX) {
            $response = $this->fetch();
            $this->ajaxReturn(1, '', $response);
        } else {
            $this->display('sync');
        }
    }

    public function sync_order()
    {
        if (\function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir=$basedir.'/data/Runtime/Data/jdjiesuan.php';
            $ret=opcache_invalidate($dir, true);
        }
        $tuiquanke_collect = F('jdjiesuan');
        $time=time();
        $starttime=I('starttime');
        $pagesize=ceil(($time-$starttime)/3600);
        if (!$tuiquanke_collect || $tuiquanke_collect===false) {
            $page = 1;
        } else {
            $page =$tuiquanke_collect['page'];
        }
        $result_data = $this->sync_scene($page, $starttime, $pagesize);
        if ($result_data) {
            $this->assign('result_data', $result_data);
            $resp = $this->fetch('collect_api');
            $this->ajaxReturn($page==$pagesize ? 0 : 1, $page==$pagesize ? '同步完成或者没有新数据了' : $page+1, $resp);
        }
    }

    protected function sync_scene($page, $starttime, $pagesize)
    {
        if (\function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir=$basedir.'/data/Runtime/Data/jdjiesuan.php';
            $ret=opcache_invalidate($dir, true);
        }
        $tuiquanke_collect = F('jdjiesuan');
        if (!$tuiquanke_collect || $page==1) {
            $coll=0;
            $totalcoll = 0;
        } else {
            $coll = $tuiquanke_collect['coll'];
            $totalcoll = $tuiquanke_collect['totalcoll'];
        }

        $starttime=$starttime+($page*3600);
        $endtime=$starttime+3600;
        $result = $this->jdorderquery($starttime, $endtime);
        $info =  json_decode($result, true);
        $json = $info['data'];
        if ($json && $json['id']) {
			
			$payMonth = strtotime($json['payMonth']);
			if($json['validCode'] == '17'){
			$ComputingTime = abs(C('yh_ComputingTime'))*86400;
			$payMonth=NOW_TIME + $ComputingTime;
			}
			
            $raw = [];
            $n=0;
            $raw['addTime']=NOW_TIME;
            $raw['oid']=$json['id'];
            $raw['orderId']=$json['orderId'];
            $raw['orderTime']=strtotime($json['orderTime']);
            $raw['finishTime']=strtotime($json['finishTime']);
            $raw['modifyTime']=strtotime($json['modifyTime']);
            $raw['skuId']=$json['skuId'];
            $raw['skuName']=$json['skuName'];
            $raw['estimateCosPrice']=$json['estimateCosPrice'];
            $raw['estimateFee']=$json['estimateFee'];
			
			if($json['validCode'] == 17){
			 $raw['estimateFee'] = $json['actualFee'];	
			}
			
            $raw['actualCosPrice']=$json['actualCosPrice'];
            $raw['actualFee']=$json['actualFee'];
            $raw['positionId']=$json['positionId'];
            $raw['validCode']=$json['validCode'];
            //$raw['payMonth']=strtotime($json['payMonth']);
			$raw['payMonth']=$payMonth;
            $raw['leve1']= trim(C('yh_bili1'));
            $raw['leve2']= trim(C('yh_bili2'));
            $raw['leve3']= trim(C('yh_bili3'));
            if ($this->_ajax_jd_order_insert($raw)) {
                $n++;
            }
        } elseif ($json) {
            $raw = [];
            $n=0;
            foreach ($json as $key => $val) {
                if ($val['id']) {
					
					$payMonth = strtotime($val['payMonth']);
					
					$estimateFee = $val['estimateFee'];
					if ($val['validCode'] == '17') { //用户收货
					    $ComputingTime = abs(C('yh_ComputingTime')) * 86400;
					    $payMonth = NOW_TIME + $ComputingTime;
						$estimateFee = $val['actualFee'];
					}
                    $raw = [
                        'addTime'=>NOW_TIME,
                        'oid'=>$val['id'],
                        'orderId'=>$val['orderId'],
                        'orderTime'=>strtotime($val['orderTime']),
                        'finishTime'=>strtotime($val['finishTime']),
                        'modifyTime'=>strtotime($val['modifyTime']),
                        'skuId'=>$val['skuId'],
                        'skuName'=>$val['skuName'],
                        'estimateCosPrice'=>$val['estimateCosPrice'],
                        'estimateFee'=>$estimateFee,
                        'actualCosPrice'=>$val['actualCosPrice'],
                        'actualFee'=>$val['actualFee'],
                        'positionId'=>$val['positionId'],
                        'validCode'=>$val['validCode'],
                        'payMonth'=>$payMonth,
                        'leve1'=> trim(C('yh_bili1')),
                        'leve2'=> trim(C('yh_bili2')),
                        'leve3'=> trim(C('yh_bili3')),
                    ];
					
                    if ($this->_ajax_jd_order_insert($raw)) {
                        $n++;
                    }
                }
            }
        } else {
            $n = 0;
        }

        $result_data['p']	 = $page;
        $result_data['msg']	 = $msg;
        $result_data['coll']		= $n;
        $result_data['totalcoll']	= $pagesize;
        $result_data['totalnum']	=   $n;
        $result_data['thiscount']	= \count($json);
        $result_data['times']		= time();
        F('jdjiesuan', [
            'coll'=>$coll,
            'page'=>$page==$pagesize ? 0 : $page+1,
            'totalcoll'=>$pagesize
        ]);

        return $result_data;
    }

    public function index()
    {
        $this->tabname='jdorder';
        $p = I('p', 1, 'intval');
        $page_size = 20;
        $start = $page_size * ($p - 1);
        $mod=M($this->tabname);
        if ($_GET['status'] && $_GET['status']!='') {
            $where['validCode']=I('status');
        }

        if ($_GET['keyword']) {
            $where['orderId'] = I('keyword', '', 'trim');
        }
		
		if($_GET['skuName']){
		    $where['skuName'] = array('like','%'.I('skuName').'%');
		}

        if ($_GET['id']) {
            $where['uid'] = I('id');
        }
   $this->assign('search', array(
            'status' => I('status'),
            'keyword' => $_GET['keyword'],
            'uid' => $_GET['id'],
        ));
        $prefix = C(DB_PREFIX);
        $field = '*,
   (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'jdorder.uid limit 1) as nickname';
        $rows = $mod->field($field)->where($where)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $mod->where($where)->count();
        $pager = new \Think\Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        foreach ($rows as $k=>$v) {
            $list[$k]['addTime']=$v['addTime'];
            $list[$k]['oid']=$v['oid'];
            $list[$k]['id']=$v['id'];
            $list[$k]['orderId']=$v['orderId'];
            $list[$k]['orderTime']=$v['orderTime'];
            $list[$k]['finishTime']=$v['finishTime'];
            $list[$k]['modifyTime']=$v['modifyTime'];
            $list[$k]['payMonth']=$v['payMonth'];
            $list[$k]['skuId']=$v['skuId'];
            $list[$k]['skuName']=$v['skuName'];
            $list[$k]['nickname']=$v['nickname'];
            $list[$k]['estimateCosPrice']=$v['estimateCosPrice'];
            $list[$k]['estimateFee']=$v['estimateFee'];
            $list[$k]['actualCosPrice']=$v['actualCosPrice'];
            $list[$k]['actualFee']=$v['actualFee'];
            $list[$k]['positionId']=$v['positionId'];
            $list[$k]['uid']=$v['uid'];
            $list[$k]['fuid']=$v['fuid'];
            $list[$k]['guid']=$v['guid'];
            $list[$k]['leve1']=$v['leve1'];
            $list[$k]['leve2']=$v['leve2'];
            $list[$k]['leve3']=$v['leve3'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['validCode']=$this->jdstatus($v['validCode']);
        }
        $this->assign('orderlist', $list);
        $this->display();
    }

    public function editorder()
    {
        $id = $this->params['id'];
        $mod  = M('jdorder');
        $info = 	$mod ->where(['id'=>$id])->find();
        if (IS_POST) {
            if ($this->params['uid']) {
                $userinfo = M('user')->field('webmaster_rate,fuid,guid')->where(['id'=>$this->params['uid']])->find();
                if ($userinfo) {
                    $data['leve1']=$userinfo['webmaster_rate'] ? $userinfo['webmaster_rate'] : trim(C('yh_bili1'));
                    $data['fuid']=$userinfo['fuid'];
                    $data['guid']=$userinfo['guid'];
                }
            }
            $data['uid'] = trim($this->params['uid']);
            $res = $mod->where(['id'=>$id])->save($data);
            if ($res !== false) {
                return $this->ajaxReturn(1, '修改成功！');
            }
            return $this->ajaxReturn(0, '修改失败！');
        }

        $this->assign('info', $info);
        $response = $this->fetch();
        $this->ajaxReturn(1, '', $response);
    }

    public function pdd_delete_f()
    {
        $mod = D('jdorder');
        $pk = $mod->getPk();
        $ids = trim(I($pk), ',');
        if ($ids) {
            if (false !== $mod->delete($ids)) {
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'));
                $this->success(L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
        } else {
            IS_AJAX && $this->ajaxReturn(0, L('illegal_parameters'));
            $this->error(L('illegal_parameters'));
        }
    }

    public function pddexport()
    {
        $start_time=I('time_start');
        $end_time=I('time_end');
        if (empty($start_time) || empty($end_time)) {
            exit('没有选择导出时间段');
        }
        $status=I('status');
        if ($status == 17) {
            $filed_time='payMonth';
        } else {
            $filed_time='orderTime';
        }
        $where[$filed_time] = [
            [
                'egt',
                strtotime($start_time)
            ],
            [
                'elt',
                strtotime($end_time)
            ]
        ];
        if ($status!='' && $status) {
            $where['validCode']= $status;
        }
        $prefix = C(DB_PREFIX);
        $field='*,
(select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'jdorder.uid limit 1) as nickname';
        $result=M('jdorder')->field($field)->where($where)->select();
        if ($result) {
            $objPHPExcel = new \PHPExcel();

            $objPHPExcel->getProperties()->setCreator("tuiquanke.com")
                               ->setLastModifiedBy("tuiquanke.com")
                               ->setTitle("订单数据导出")
                               ->setSubject("订单数据导出")
                               ->setDescription("备份数据")
                               ->setKeywords("excel")
                               ->setCategory("result file");

            $objPHPExcel->getActiveSheet()->setCellValue('A1', '订单号');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', '商品名');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', '付款时间');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', '付款');
            $objPHPExcel->getActiveSheet()->setCellValue('E1', '结算时间');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', '商品链接');
            $objPHPExcel->getActiveSheet()->setCellValue('G1', '推广位');
            $objPHPExcel->getActiveSheet()->setCellValue('H1', '返利比例');
            $objPHPExcel->getActiveSheet()->setCellValue('I1', '预估收入');

            foreach ($result as $k => $v) {
                $num=$k+2;
                if ($v['payMonth']) {
                    $up_time=date('Y-m-d H:i:s', $v['payMonth']);
                } else {
                    $up_time='--';
                }

                //$income=round($v['estimateFee']*($v['rates']/100),2);
                $objPHPExcel->setActiveSheetIndex(0)
                          ->setCellValue('A'.$num, ' '.$v['orderId'])
                          ->setCellValue('B'.$num, ' '.$v['skuName'])
                          ->setCellValue('C'.$num, date('Y-m-d H:i:s', $v['orderTime']))
                          ->setCellValue('D'.$num, $v['estimateCosPrice'])
                          ->setCellValue('E'.$num, $up_time)
                          ->setCellValue('F'.$num, 'https://item.jd.com/'.$v['skuId'].'.html')
                          ->setCellValue('G'.$num, $v['positionId'])
                          ->setCellValue('H'.$num, $v['leve1'])
                          ->setCellValue('I'.$num, $v['estimateFee']);
            }

            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.date('Y-m-d', NOW_TIME).'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }

        exit('no data');
    }
}
