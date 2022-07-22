<?php
/**
 * TOP API: taobao.tae.item.detail.get request
 *
 * @author auto create
 * @since 1.0, 2016.08.03
 */
class ItemDetailGetRequest
{
	private $params;
	
	private $fields;
	
	private $item_id;
	
	private $apiParas = array();
	
	public function setParams($params)
	{
		$this->params = $params;
		$this->apiParas["params"] = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setItemId($item_id)
	{
		$this->item_id = $item_id;
		$this->apiParas["item_id"] = $item_id;
	}

	public function getItemId()
	{
		return $this->item_id;
	}
	
	public function getApiMethodName()
	{
		return "taobao.item.detail.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->fields,"fields");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
