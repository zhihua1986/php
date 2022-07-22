<?php


namespace Duomai\CpsClient\Network\Interfaces;


use Psr\Http\Message\ResponseInterface;

interface EndpointInterface
{
    /**
     * @return string
     */
    public function Service();

    /**
     * @return string
     */
    public function Method();

    /**
     * @return array
     */
    public function getBody();

    /**
     * @return array
     */
    public function getResult();

    /**
     * @return bool
     */
    public function IsSuccess();

    /**
     * @return string
     */
    public function Error();

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function SetHttpResult(ResponseInterface $response);

}