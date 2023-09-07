<?php

/**
 * 查询rquest
 * @author auto create
 */
class SingleStorePromotionRequest
{
	
	/** 
	 * 活动ID
	 **/
	public $activity_id;
	
	/** 
	 * 是否返回微信推广图片
	 **/
	public $include_wx_img;
	
	/** 
	 * 媒体出资活动ID
	 **/
	public $media_activity_id;
	
	/** 
	 * 渠道PID
	 **/
	public $pid;
	
	/** 
	 * 门店ID（加密，具有时效性，建议每天更新一次）
	 **/
	public $shop_id;
	
	/** 
	 * 三方扩展id
	 **/
	public $sid;	
}
?>