<?php

namespace Duomai\CpsClient\Endpoints;

use Duomai\CpsClient\Exceptions\ServiceException;
use Duomai\CpsClient\Network\EndpointBase;

/**
 * 转链接口
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Endpoints
 */
class ChanLink extends EndpointBase
{
    /**
     * ChanLink constructor.
     * @param $siteID
     * @param $productID
     * @param $adsID
     * @param $url
     * @param $original
     * @param $ext
     * @throws ServiceException
     */
    public function __construct($siteID, $productID, $adsID, $url, $original, $ext)
    {
        if (empty($siteID)) {
            throw new ServiceException("推广位id不能为空");
        }
        if (empty($productID) && empty($adsID) && empty($url)) {
            throw new ServiceException("商品id 计划id 与url 必须传入一个参数");
        }
        $this->params = [
            "site_id" => $siteID,
            "product_id" => $productID,
            "ads_id" => $adsID,
            "url" => $url,
            "original" => $original,
            "ext" => $ext
        ];
    }

    public function Service()
    {
        return "cps-mesh.cpslink.links.post";
    }

    public function getResult()
    {
        return $this->data["data"];
    }
}