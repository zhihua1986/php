<?php

namespace Duomai\CpsClient\Endpoints;

use Duomai\CpsClient\Network\EndpointBase;

/**
 * 计划详情
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Endpoints
 */
class PlanDetail extends EndpointBase
{

    /**
     * EncryptLink constructor.
     * @param $adsId
     */
    public function __construct($adsId)
    {
        $this->params = [
            "ads_id" => $adsId
        ];
    }

    public function Service()
    {
        return "cps-mesh.open.stores.detail.get";
    }

    public function getResult()
    {
        return $this->data["data"];
    }

}