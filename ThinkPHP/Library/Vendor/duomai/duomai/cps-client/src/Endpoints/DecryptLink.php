<?php

namespace Duomai\CpsClient\Endpoints;

use Duomai\CpsClient\Exceptions\ServiceException;
use Duomai\CpsClient\Network\EndpointBase;

/**
 * 链接解密
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Endpoints
 */
class DecryptLink extends EndpointBase
{

    /**
     * DecryptLink constructor.
     * @param $url
     * @throws ServiceException
     */
    public function __construct($url)
    {
        if(empty($url)){
            throw new ServiceException("url参数不能为空");
        }
        $this->params = [
            "url" => $url,
        ];
    }

    public function Service()
    {
        return "cps-mesh.cpslink.links.crypt.get";
    }

    public function getResult()
    {
        return $this->data["data"];
    }
}