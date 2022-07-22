<?php


namespace Duomai\CpsClient\Endpoints;


use Duomai\CpsClient\Network\EndpointBase;

/**
 * 自定义接口请求
 * Class RequestParams
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Endpoints
 */
class RequestParams extends EndpointBase
{
    protected $service;

    public function __construct($service, $params)
    {
        $this->service = $service;
        $this->params = $params;
    }

    public function Service()
    {
        return $this->service;
    }

}