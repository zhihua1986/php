<?php

namespace Duomai\CpsClient\Endpoints;

use Duomai\CpsClient\Exceptions\ServiceException;
use Duomai\CpsClient\Network\EndpointBase;

/**
 * 计划列表
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Endpoints
 */
class PlanList extends EndpointBase
{
    /**
     * EncryptLink constructor.
     * @param string $query
     * @param int $isApply
     * @param int $page
     * @param int $pageSize
     */
    public function __construct($query = "", $isApply = 0, $page = 1, $pageSize = 20)
    {
        $this->params = [
            "page" => $page,
            "page_size" => $pageSize,
        ];
        if (empty($query)) {
            $this->params["query"] = $query;
        }
        if (empty($isApply)) {
            $this->params["is_apply"] = $query;
        }
    }

    public function Service()
    {
        return "cps-mesh.open.stores.plans.get";
    }

}