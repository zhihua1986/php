<?php

namespace Duomai\CpsClient\Endpoints;

use Duomai\CpsClient\Network\EndpointBase;

/**
 * 短链接
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Endpoints
 */
class ShortLink extends EndpointBase
{
    /**
     * ShortLink constructor.
     * @param string $url 长链接
     * @param int $type 0 多麦默认链接 1 多麦微博防拼搏 2 url.cn短链
     */
    public function __construct($url, $type = 0)
    {
        $this->params = [
            "url" => $url,
            "type" => $type,
        ];
    }

    public function Service()
    {
        return "cps-mesh.cpslink.links.short.post";
    }

    public function Method()
    {
        return "Post";
    }

    public function getResult()
    {
        return $this->data["data"];
    }
}