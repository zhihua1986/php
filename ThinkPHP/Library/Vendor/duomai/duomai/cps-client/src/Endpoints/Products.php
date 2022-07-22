<?php


namespace Duomai\CpsClient\Endpoints;


use Duomai\CpsClient\Network\EndpointBase;

/**
 * 商品列表 
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Endpoints
 */
class Products extends EndpointBase
{
    const PLATFORM_YOUZAN = "youzan";
    const ALIMAMA_YOUZAN = "alimama";
    const PLATFORM_PDD = "pdd";
    const PLATFORM_KAOLA = "kaola";
    const PLATFORM_VIP = "vip";
    const PLATFORM_SUNING = "suning";
    const PLATFORM_1688 = "b1688";
    const PLATFORM_JDUnion = "jd";

    private $platform = self::PLATFORM_JDUnion;

    public function __construct($query, $platform)
    {
        $this->platform = $platform;
        $this->params = $query;
    }

    public function Service()
    {
        return "cps-mesh.cpslink.{$this->platform}.products.get";
    }
}
