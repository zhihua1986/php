<?php
/**
 * TOP API: alibaba.alsc.union.eleme.promotion.storepromotion.query request
 * 
 * @author auto create
 * @since 1.0, 2023.08.23
 */
class AlibabaAlscUnionElemePromotionStorepromotionQueryRequest
{
	/** 
	 * 查询rquest
	 **/
	private $queryRequest;
	
	private $apiParas = array();
	
	public function setQueryRequest($queryRequest)
	{
		$this->queryRequest = $queryRequest;
		$this->apiParas["query_request"] = $queryRequest;
	}

	public function getQueryRequest()
	{
		return $this->queryRequest;
	}

	public function getApiMethodName()
	{
		return "alibaba.alsc.union.eleme.promotion.storepromotion.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
