<?php

/**
 * 查询rquest
 * @author auto create
 */
class PromotionQueryRequest
{
	
	/** 
	 * 指定召回供给枚举
	 **/
	public $biz_type;
	
	/** 
	 * 城市编码（只用于经纬度覆盖多个城市时过滤）
	 **/
	public $city_id;
	
	/** 
	 * 以一级类目进行类目限定，以,或者|进行类目分隔
	 **/
	public $filter_first_categories;
	
	/** 
	 * 1.5级类目查询，以"|"分隔
	 **/
	public $filter_one_point_five_categories;
	
	/** 
	 * 否当前有c端奖励金活动库存（默认false不做过滤）
	 **/
	public $has_bonus_stock;
	
	/** 
	 * 是否参与奖励金活动（默认false不做过滤）
	 **/
	public $in_activity;
	
	/** 
	 * 纬度
	 **/
	public $latitude;
	
	/** 
	 * 经度
	 **/
	public $longitude;
	
	/** 
	 * 媒体出资活动ID
	 **/
	public $media_activity_id;
	
	/** 
	 * 店铺佣金比例下限，代表筛选店铺全店佣金大于等于0.01的店铺
	 **/
	public $min_commission_rate;
	
	/** 
	 * 每页数量（1~20，默认20）
	 **/
	public $page_size;
	
	/** 
	 * 渠道PID
	 **/
	public $pid;
	
	/** 
	 * 检索内容（支持门店名称）
	 **/
	public $search_content;
	
	/** 
	 * 会话ID（分页场景首次请求结果返回，后续请求必须携带，服务根据session_id相同请求次数自动翻页返回）
	 **/
	public $session_id;
	
	/** 
	 * 三方扩展id
	 **/
	public $sid;
	
	/** 
	 * 排序类型，默认normal，排序规则包括:{"normal":"佣金倒序","distance":"距离由近到远","commission":"佣金倒序","monthlySale":"月销量","couponAmount":"叠加券金额倒序","activityReward":"奖励金金额倒序","commissionRate":"佣金比例倒序"}
	 **/
	public $sort_type;	
}
?>