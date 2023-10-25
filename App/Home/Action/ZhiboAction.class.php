<?php

namespace Home\Action;

use Common\Api\Weixin;
use Common\Model\userModel;
use Duomai\CpsClient\Client;
use Duomai\CpsClient\Endpoints\Products;

class ZhiboAction extends BaseAction
{

    public function _initialize()
    {
        parent::_initialize();

    }

     public function index()
     {
/*

         $pid = trim(C('yh_youhun_secret'));

             $where = [
                 'type' => 'pdd.ddk.resource.url.gen',
                 'data_type' => 'JSON',
                 'timestamp' => $this->msectime(),
                 'client_id' => trim(C('yh_pddappkey')),
                 'generate_we_app' => 'true',
                 'resource_type' => 39998,
                 'pid' => $pid,
                 'url'=>'https://mobile.yangkeduo.com/promotion_op.html?type=59&id=195312',
             ];

             if ($uid) {
                 $where['custom_parameters'] = $uid;
             }

             $where['sign'] = $this->create_pdd_sign(trim(C('yh_pddsecretkey')), $where);
             $pdd_api = 'http://gw-api.pinduoduo.com/api/router';
             $result = $this->_curl($pdd_api, $where, true);
             $data = json_decode($result, true);

             dump($data);




         exit;
         */




     }
}

?>

