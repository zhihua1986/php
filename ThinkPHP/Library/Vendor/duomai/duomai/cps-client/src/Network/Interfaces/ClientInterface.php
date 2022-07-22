<?php


namespace Duomai\CpsClient\Network\Interfaces;



interface ClientInterface
{
    /**
     * ClientInterface constructor.
     * @param array $config
     */
    public function __construct($config = []);

    /**
     * @param EndpointInterface $ser
     * @return EndpointInterface
     */
    public function doService(EndpointInterface $ser);
}