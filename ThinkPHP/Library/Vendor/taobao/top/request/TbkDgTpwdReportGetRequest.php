<?php
/**
 * TOP API: taobao.tbk.dg.tpwd.report.get request
 * 
 * @author auto create
 * @since 1.0, 2021.11.25
 */
class TbkDgTpwdReportGetRequest
{
	/** 
	 * mm_xxx_xxx_xxx的第3段数字
	 **/
	private $adzoneId;
	
	/** 
	 * 待查询的口令
	 **/
	private $taoPassword;
	
	private $apiParas = array();
	
	public function setAdzoneId($adzoneId)
	{
		$this->adzoneId = $adzoneId;
		$this->apiParas["adzone_id"] = $adzoneId;
	}

	public function getAdzoneId()
	{
		return $this->adzoneId;
	}

	public function setTaoPassword($taoPassword)
	{
		$this->taoPassword = $taoPassword;
		$this->apiParas["tao_password"] = $taoPassword;
	}

	public function getTaoPassword()
	{
		return $this->taoPassword;
	}

	public function getApiMethodName()
	{
		return "taobao.tbk.dg.tpwd.report.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->adzoneId,"adzoneId");
		RequestCheckUtil::checkNotNull($this->taoPassword,"taoPassword");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
