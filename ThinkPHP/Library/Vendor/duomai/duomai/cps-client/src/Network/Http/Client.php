<?php

namespace Duomai\CpsClient\Network\Http;

use Duomai\CpsClient\Exceptions\ServiceException;
use Duomai\CpsClient\Network\Interfaces\ClientInterface;
use Duomai\CpsClient\Network\Interfaces\EndpointInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Client
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Network\Client
 */
class Client extends \GuzzleHttp\Client implements ClientInterface
{
    protected $appKey;

    protected $appSecret;

    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (isset($config["auth"])) {
            $this->appKey = isset($config["auth"]["app_key"]) ? $config["auth"]["app_key"] : "";
            $this->appSecret = isset($config["auth"]["app_secret"]) ? $config["auth"]["app_secret"] : "";
        }
        unset($config["auth"]);
        parent::__construct($config);
    }


    /**
     * @param EndpointInterface $ser
     * @return EndpointInterface
     */
    public function doService(EndpointInterface $ser)
    {
        $header = [
            "Content-Type" => "application/json"
        ];
        $query = [
            "app_key" => $this->appKey,
            "timestamp" => time(),
            "service" => $ser->Service()
        ];
        ksort($query);
        $signStr = '';
        foreach ($query as $kev => $val) {
            $signStr .= $kev . $val;
        }
        $body = \GuzzleHttp\json_encode($ser->getBody());
        $query["sign"] = strtoupper(md5($this->appSecret . $signStr . $body . $this->appSecret));
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', "https://open.duomai.com/apis", [
                "verify" => false,
                "headers" => $header,
                "query" => $query,
                "body" => $body
            ]);
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
        }
        $ser->SetHttpResult($response);
        return $ser;
    }
}