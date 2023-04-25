<?php

/**
 * 查询rquest
 * @author auto create
 */
class ActivityRequest
{
	
	/** 
	 * 活动ID
	 **/
	public $activity_id;
	
	/** 
	 * 是否包含二维码，如果为false，不返回二维码和图片，只有链接
	 **/
	public $include_qr_code;
	
	/** 
	 * 是否返回微信推广图片
	 **/
	public $include_wx_img;
	
	/** 
	 * 渠道PID
	 **/
	public $pid;
	
	/** 
	 * 三方会员id。长度限制50
	 **/
	public $sid;	
}
?>