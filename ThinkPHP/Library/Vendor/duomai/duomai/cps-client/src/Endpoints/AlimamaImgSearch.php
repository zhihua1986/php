<?php


namespace Duomai\CpsClient\Endpoints;


use Duomai\CpsClient\Exceptions\ServiceException;
use Duomai\CpsClient\Network\EndpointBase;

/**
 * 拍立淘
 * Class AlimamaImgSearch
 * @author real<real@goldenname.com>
 * @since 1.0
 * @package Duomai\CpsClient\Endpoints
 */
class AlimamaImgSearch extends EndpointBase
{
    /**
     * ChanLink constructor.
     * @param string $imgBase64 图片数据
     * @param string $imgKey 图片key
     * @param string $cat
     * @throws ServiceException
     */
    public function __construct($imgBase64, $imgKey=null, $cat=null)
    {
        if (empty($imgBase64) && empty($imgKey)) {
            throw new ServiceException("图片数据或者图片key必须传入一个");
        }
        if(!empty($cat)){
            $this->params["cat"] = $cat;
        }
        if (!empty($imgKey)) {
            $this->params["img_key"] = $imgKey;
        } else {
            $this->params["img_base64"] = $imgBase64;
        }
    }

    public function Service()
    {
        return "cps-mesh.cpslink.alimama.products.img.post";
    }

    public function getResult()
    {
        return $this->data["data"];
    }
}