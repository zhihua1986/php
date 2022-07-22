<?php


namespace Duomai\CpsClient\Endpoints;


use Duomai\CpsClient\Exceptions\ServiceException;
use Duomai\CpsClient\Network\EndpointBase;

/**
 * Class OrderDetail
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Endpoints
 */
class OrderDetail extends EndpointBase
{

    /**
     * EncryptLink constructor.
     * @param $adsId
     * @param $orderSn
     * @throws ServiceException
     */
    public function __construct($adsId, $orderSn)
    {
        $this->params = [
            "ads_id" => $adsId,
            "order_sn" => $orderSn,
        ];
    }

    public function Service()
    {
        return "cps-mesh.open.orders.detail.get";
    }

    public function getResult()
    {
        return $this->data["data"];
    }

}