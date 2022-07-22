<?php


namespace Duomai\CpsClient\Endpoints;


use Duomai\CpsClient\Exceptions\ServiceException;
use Duomai\CpsClient\Network\EndpointBase;

/**
 * Class OrderList
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Endpoints
 */
class OrderList extends EndpointBase
{

    /**
     * EncryptLink constructor.
     * @param $stime
     * @param $etime
     * @param $page
     * @param $pageSize
     * @param $orderField
     * @param array $query 可选参数 ["site_id"=>"site_id","ads_id"=>"ads_id","euid"=>"euid","status"=>"status"]
     * @throws ServiceException
     */
    public function __construct($stime, $etime, $page, $pageSize,$orderField, $query = [])
    {
        if(!in_array($orderField,["update_time","chargedatatime","orderdatatime"])){
            $orderField = "update_time";
        }
        $stime = is_numeric($stime) ? $stime : strtotime($stime);
        $etime = is_numeric($etime) ? $etime : strtotime($etime);
        if (empty($stime) || empty($etime)) {
            throw new ServiceException("时间范围不能为空");
        }
        $this->params = [
            "stime" => $stime,
            "etime" => $etime,
            "page" => $page,
            "page_size" => $pageSize,
            "order_field" => $orderField,
        ];
        if(!empty($query)){
            $this->params = array_merge($query, $this->params);
        }
    }

    public function Service()
    {
        return "cps-mesh.open.orders.query.get";
    }

}