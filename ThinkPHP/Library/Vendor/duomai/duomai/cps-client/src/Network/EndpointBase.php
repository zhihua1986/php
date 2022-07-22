<?php


namespace Duomai\CpsClient\Network;

use Duomai\CpsClient\Network\Interfaces\EndpointInterface;
use GuzzleHttp\Exception\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class EndpointBase
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Network
 */
abstract class EndpointBase implements EndpointInterface
{
    protected $success = false;

    protected $error = "";

    protected $data = [];

    protected $params;

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->data;
    }

    public function IsSuccess()
    {
        return $this->success;
    }

    public function Error()
    {
        return $this->error;
    }

    public function getBody()
    {
        return $this->params;
    }

    public function Method()
    {
        return "POST";
    }

    public function SetHttpResult(ResponseInterface $response)
    {
        $body = $response->getBody()->getContents();
        try {
            $this->data = \GuzzleHttp\json_decode($body, true);
        } catch (InvalidArgumentException $e) {
            $this->data = [
                "status" => 500,
                "message" => "服务异常返回非json格式 :" . $body,
            ];
        }
        if (empty($this->data["status"])) {
            $this->success = true;
        } else {
            $this->error = $this->data["message"] ? $this->data["message"] : json_encode($this->data);
        }
    }
}