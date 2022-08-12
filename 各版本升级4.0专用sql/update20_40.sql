-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- Host: localhost
-- Generation Time: 2017-09-12 16:55:27
-- 服务器版本： 5.6.37-log
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tuiquankecms`
--

-- --------------------------------------------------------

--
-- 表的结构 `tqk_apply`
--
DROP TABLE IF EXISTS `tqk_apply`;
CREATE TABLE IF NOT EXISTS `tqk_apply` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `alipay` varchar(80) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `qq` varchar(12) DEFAULT NULL,
  `add_time` varchar(15) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------
--
-- 表的结构 `tqk_basklist`
--
DROP TABLE IF EXISTS `tqk_basklist`;
CREATE TABLE IF NOT EXISTS `tqk_basklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `orderid` int(11) DEFAULT NULL COMMENT '//订单id',
  `order_sn` varchar(50) DEFAULT NULL COMMENT '//订单号',
  `content` varchar(255) DEFAULT NULL,
  `img` varchar(100) DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL,
  `create_time` varchar(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `integray` mediumint(5) DEFAULT NULL COMMENT '//积分',
  `type` tinyint(1) DEFAULT '0' COMMENT '//1为精华帖，2为普通帖',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_basklist_logo`
--
CREATE TABLE IF NOT EXISTS `tqk_basklist_logo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `order_sn` varchar(50) DEFAULT NULL,
  `integray` mediumint(5) DEFAULT NULL COMMENT '//积分',
  `remark` varchar(50) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_finance`
--
DROP TABLE IF EXISTS `tqk_finance`;
CREATE TABLE IF NOT EXISTS `tqk_finance` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `add_time` varchar(15) DEFAULT NULL,
  `price` float(10,2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `backcash` float(10,2) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `income` float(10,2) DEFAULT NULL,
  `mark` varchar(200) DEFAULT NULL,
  `type` TINYINT( 1 ) NULL DEFAULT  '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


--
-- 表的结构 `tqk_brand`
--
DROP TABLE IF EXISTS `tqk_brand`;
CREATE TABLE IF NOT EXISTS `tqk_brand` (
  `id` int(11) NOT NULL,
  `cate_id` smallint(4) DEFAULT NULL,
  `logo` varchar(50) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `recommend` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `ordid` mediumint(5) DEFAULT '255',
  `add_time` varchar(20) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;


INSERT INTO `tqk_brand` (`id`, `cate_id`, `logo`, `brand`, `recommend`, `status`, `ordid`, `add_time`, `remark`) VALUES
(7, 14, '/data/upload/avatar/5a2df3e952156.png', '九阳', 1, 1, 255, NULL, '最高可省200元'),
(8, 17, '/data/upload/avatar/5a2df5618446a.png', '波司登', 1, 1, 255, NULL, ''),
(9, 11, '/data/upload/avatar/5a2e14cdbbb50.jpg', '百雀羚', 1, 1, 255, NULL, ''),
(10, 11, '/data/upload/avatar/5a2e14dc6892c.jpg', '佰草集', 1, 1, 255, NULL, ''),
(11, 11, '/data/upload/avatar/5a2e14f00e03c.jpg', '菲诗小铺', 1, 1, 255, NULL, ''),
(12, 13, '/data/upload/avatar/5a2fe38dba8f5.jpg', '苏泊尔', 1, 1, 255, NULL, ''),
(13, 17, '/data/upload/avatar/5a2fe543d3481.jpg', '恒源祥', 1, 1, 255, NULL, ''),
(14, 11, '/data/upload/avatar/5a2fe69461f7b.jpg', '自然堂', 1, 1, 255, NULL, ''),
(15, 9, '/data/upload/avatar/5a3128ad23039.jpg', '百草味', 1, 1, 255, NULL, ''),
(16, 9, '/data/upload/avatar/5a3128d2db7cb.jpg', '良品铺子', 1, 1, 255, NULL, ''),
(17, 9, '/data/upload/avatar/5a3128e4270b1.jpg', '徐福记', 1, 1, 255, NULL, ''),
(18, 9, '/data/upload/avatar/5a3129058c49d.jpg', '盼盼食品', 1, 1, 255, NULL, ''),
(19, 9, '/data/upload/avatar/5a31293e48ee6.jpg', '雀巢', 1, 1, 255, NULL, ''),
(20, 10, '/data/upload/avatar/5a31296944578.jpg', '施华洛世奇', 1, 1, 255, NULL, ''),
(21, 10, '/data/upload/avatar/5a31297eb3c32.jpg', '六福珠宝', 1, 1, 255, NULL, ''),
(22, 10, '/data/upload/avatar/5a31298fa3e41.jpg', '慈元阁', 1, 1, 255, NULL, ''),
(23, 11, '/data/upload/avatar/5a3129b124cea.jpg', '韩后', 1, 1, 255, NULL, ''),
(24, 16, '/data/upload/avatar/5a3129cb05949.jpg', '十月结晶', 1, 1, 255, NULL, ''),
(25, 17, '/data/upload/avatar/5a3129e761a57.jpg', '七匹狼', 1, 1, 255, NULL, ''),
(26, 17, '/data/upload/avatar/5a312a35aaa86.jpg', '雅鹿', 1, 1, 255, NULL, ''),
(27, 17, '/data/upload/avatar/5a312a45b3c5c.jpg', '红豆内衣', 1, 1, 255, NULL, ''),
(28, 18, '/data/upload/avatar/5a312a61012a0.jpg', '特步', 1, 1, 255, NULL, ''),
(29, 18, '/data/upload/avatar/5a312a735447e.jpg', '达芙妮', 1, 1, 255, NULL, ''),
(30, 12, '/data/upload/avatar/5a312a84d9e9b.jpg', '晨光', 1, 1, 255, NULL, ''),
(31, 12, '/data/upload/avatar/5a312a98e3c79.jpg', '得力', 1, 1, 255, NULL, ''),
(32, 15, '/data/upload/avatar/5a312aab01ea9.jpg', '稻草人', 1, 1, 255, NULL, ''),
(33, 15, '/data/upload/avatar/5a312ac130780.jpg', '老人头', 1, 1, 255, NULL, ''),
(34, 14, '/data/upload/avatar/5a312b117e0f6.jpg', '品胜', 1, 1, 255, NULL, ''),
(35, 14, '/data/upload/avatar/5a312b301b3e6.jpg', '罗技', 1, 1, 255, NULL, ''),
(36, 14, '/data/upload/avatar/5a312b551f218.jpg', '海尔', 1, 1, 255, NULL, ''),
(37, 14, '/data/upload/avatar/5a312b6423e16.jpg', '飞利浦', 1, 1, 255, NULL, ''),
(38, 18, '/data/upload/avatar/5a312b8e6e0a2.jpg', '李宁', 1, 1, 255, NULL, ''),
(39, 17, '/data/upload/avatar/5a312c0c17d83.jpg', '迪士尼', 1, 1, 255, NULL, ''),
(40, 11, '/data/upload/avatar/5a312c42d930f.jpg', '美肤宝', 1, 1, 255, NULL, ''),
(41, 11, '/data/upload/avatar/5a312d4c815dd.jpg', '欧莱雅', 1, 1, 255, NULL, ''),
(42, 11, '/data/upload/avatar/5a312d6912935.jpg', '欧诗漫', 1, 1, 255, NULL, '');

--
-- Indexes for table `tqk_brand`
--
ALTER TABLE `tqk_brand`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `tqk_brand`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=43;
  
--
-- 表的结构 `tqk_brand_cate`
--
DROP TABLE IF EXISTS `tqk_brand_cate`;
CREATE TABLE IF NOT EXISTS `tqk_brand_cate` (
  `id` smallint(4) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `pid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `spid` varchar(50) NOT NULL,
  `ordid` smallint(4) unsigned NOT NULL DEFAULT '255',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

INSERT INTO `tqk_brand_cate` (`id`, `name`, `pid`, `spid`, `ordid`, `status`, `seo_title`, `seo_keys`, `seo_desc`) VALUES
(9, '美食', 0, '0', 255, 1, '', '', ''),
(10, '配饰', 0, '0', 255, 1, '', '', ''),
(11, '美妆', 0, '0', 255, 1, '', '', ''),
(12, '文体', 0, '0', 255, 1, '', '', ''),
(13, '居家', 0, '0', 255, 1, '', '', ''),
(14, '数码电器', 0, '0', 255, 1, '', '', ''),
(15, '箱包', 0, '0', 255, 1, '', '', ''),
(16, '母婴', 0, '0', 255, 1, '', '', ''),
(17, '服装', 0, '0', 255, 1, '', '', ''),
(18, '鞋帽', 0, '0', 255, 1, '', '', '');

ALTER TABLE `tqk_brand_cate`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `tqk_brand_cate`
  MODIFY `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;

--
-- 表的结构 `tqk_nav`
--
DROP TABLE IF EXISTS `tqk_nav`;
 CREATE TABLE IF NOT EXISTS `tqk_nav` (
  `id` smallint(4) unsigned NOT NULL,
  `type` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `alias` varchar(20) NOT NULL,
  `link` varchar(255) NOT NULL,
  `target` tinyint(1) NOT NULL DEFAULT '1',
  `ordid` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `mod` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


INSERT INTO `tqk_nav` (`id`, `type`, `name`, `alias`, `link`, `target`, `ordid`, `mod`, `status`) VALUES
(1, 'main', '超级人气榜', 'top100', '/index.php/top100', 1, 2, '', 1),
(2, 'main', '特卖精选', 'jingxuan', '/index.php/jingxuan', 0, 3, '', 1),
(3, 'main', '品牌优惠券', 'brand', '/index.php/brand', 0, 5, '', 1),
(4, 'main', '九块九包邮', 'jiu', '/index.php/jiu', 0, 4, '', 1),
(6, 'main', '优惠券头条', 'article', '/index.php/article/', 0, 255, '', 1),
(7, 'main', '申请代理', 'apply', '/index.php/apply', 0, 255, '', 1),
(8, 'main', '晒单赚积分', 'shaidan', '/index.php/basklist', 0, 255, '', 1);

ALTER TABLE `tqk_nav`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `tqk_nav`
  MODIFY `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
  


-- --------------------------------------------------------
--
-- 表的结构 `tqk_order`
--

ALTER TABLE  `tqk_order` CHANGE  `uid`  `uid` INT( 10 ) NULL DEFAULT  '0';

ALTER TABLE  `tqk_order` ADD  `goods_iid` VARCHAR( 15 ) NULL AFTER  `up_time` ,
ADD  `goods_title` VARCHAR( 160 ) NULL AFTER  `goods_iid` ,
ADD  `goods_num` INT( 3 ) NULL AFTER  `goods_title` ,
ADD  `ad_id` VARCHAR( 50 ) NULL AFTER  `goods_num` ,
ADD  `ad_name` VARCHAR( 80 ) NULL AFTER  `ad_id` ,
ADD  `goods_rate` VARCHAR( 10 ) NULL AFTER  `ad_name` ,
ADD  `oid` VARCHAR( 60 ) NULL ,
ADD  `income` FLOAT( 10, 2 ) NULL AFTER  `goods_rate` ;


ALTER TABLE  `tqk_user` ADD  `webmaster` TINYINT( 1 ) NULL DEFAULT  '0',
ADD  `webmaster_pid` VARCHAR( 30 ) NULL ,
ADD  `pdd_pid` VARCHAR( 30 ) NULL ,
ADD  `oid` VARCHAR( 60 ) NULL ,
ADD  `tbname` VARCHAR( 50 ) NULL ,
ADD  `webmaster_rate` INT( 5 ) NULL ;



alter table tqk_admin_role rename tqk_adminrole;
alter table tqk_admin_auth rename tqk_adminauth;
alter table tqk_article_cate rename tqk_articlecate;
alter table tqk_basklist_logo rename tqk_basklistlogo;
alter table tqk_hongbao_detail rename tqk_hongbaodetail;
alter table tqk_items_cate rename tqk_itemscate;
alter table tqk_items_like rename tqk_itemslike;
alter table tqk_items_orig rename tqk_itemsorig;
alter table tqk_user_cash rename tqk_usercash;
alter table tqk_brand_cate rename tqk_brandcate;


--
-- 表的结构 `tqk_pdditems`
--
DROP TABLE IF EXISTS `tqk_pdditems`;
CREATE TABLE IF NOT EXISTS `tqk_pdditems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` varchar(20) NOT NULL,
  `goods_name` varchar(255) NOT NULL,
  `goods_desc` varchar(255) DEFAULT NULL,
  `goods_thumbnail_url` varchar(120) NOT NULL,
  `goods_image_url` varchar(120) NOT NULL,
  `sold_quantity` int(11) NOT NULL,
  `min_group_price` decimal(11,2) NOT NULL,
  `min_normal_price` decimal(11,2) NOT NULL,
  `mall_name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `coupon_discount` varchar(20) NOT NULL,
  `coupon_total_quantity` int(11) NOT NULL,
  `coupon_remain_quantity` int(11) NOT NULL,
  `coupon_start_time` int(11) NOT NULL,
  `coupon_end_time` int(11) NOT NULL,
  `addtime` int(11) NOT NULL,
  `quanurl` varchar(200) DEFAULT NULL,
  `orderid` int(5) NOT NULL DEFAULT '999',
  `tuisong` tinyint(1) NOT NULL DEFAULT '0',
  `promotion_rate` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='拼多多商品表' AUTO_INCREMENT=1 ;


--
-- 表的结构 `tqk_menu`
--
DROP TABLE IF EXISTS `tqk_menu`;
CREATE TABLE IF NOT EXISTS `tqk_menu` (
  `id` smallint(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `pid` smallint(6) NOT NULL,
  `module_name` varchar(20) NOT NULL,
  `action_name` varchar(20) NOT NULL,
  `data` varchar(120) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `often` tinyint(1) NOT NULL DEFAULT '0',
  `ordid` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `display` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=385 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_menu`
--

INSERT INTO `tqk_menu` (`id`, `name`, `pid`, `module_name`, `action_name`, `data`, `remark`, `often`, `ordid`, `display`) VALUES
(1, '网站管理', 0, 'setting', 'index', '', '', 0, 1, 1),
(2, '核心设置', 1, 'setting', 'index', '', '', 0, 1, 1),
(3, '首页参数', 151, 'setting', 'index', '&amp;type=site', '', 0, 1, 1),
(6, '菜单管理', 2, 'menu', 'index', '', '', 0, 4, 1),
(7, '新增', 6, 'menu', 'add', '', '', 0, 0, 0),
(8, '编辑', 6, 'menu', 'edit', '', '', 0, 0, 0),
(9, '删除', 6, 'menu', 'delete', '', '', 0, 0, 0),
(14, '友情链接', 288, 'link', 'index', '', '', 0, 2, 1),
(15, '友情链接', 338, 'link', 'index', '', '', 0, 0, 1),
(17, '新增', 15, 'link', 'add', '', '', 0, 0, 0),
(18, '编辑', 15, 'link', 'edit', '', '', 0, 0, 0),
(19, '删除', 15, 'link', 'delete', '', '', 0, 0, 0),
(31, '数据库管理', 331, 'backup', 'index', '', '', 0, 2, 1),
(32, '数据备份', 31, 'backup', 'index', '', '', 0, 0, 1),
(33, '数据恢复', 31, 'backup', 'restore', '', '', 0, 0, 1),
(34, '清理缓存', 2, 'cache', 'index', '', '', 1, 0, 0),
(50, '数据管理', 0, 'content', 'index', '', '', 0, 2, 1),
(51, '商品管理', 50, 'article', 'index', '', '', 0, 2, 1),
(52, '商品管理', 51, 'items', 'index', '', '', 0, 2, 1),
(54, '编辑', 52, 'article', 'edit', '', '', 0, 3, 0),
(56, '商品分类', 292, 'itemscate', 'index', '', '', 0, 6, 1),
(60, '管理员管理', 1, 'admin', 'index', '', '', 0, 3, 1),
(61, '管理员管理', 60, 'admin', 'index', '', '', 0, 1, 1),
(62, '新增', 61, 'admin', 'add', '', '', 0, 0, 0),
(63, '编辑', 61, 'admin', 'edit', '', '', 0, 0, 0),
(64, '删除', 61, 'admin', 'delete', '', '', 0, 0, 0),
(65, '分组管理', 60, 'adminrole', 'index', '', '', 0, 2, 1),
(66, '新增', 65, 'adminrole', 'add', '', '', 0, 0, 0),
(148, '站点设置', 2, 'setting', 'index', '', '', 0, 0, 1),
(150, '删除', 149, 'user', 'delete', '', '', 0, 5, 0),
(151, '首页设置', 1, 'nav', 'index', '', '', 0, 1, 1),
(152, '导航设置', 151, 'nav', 'index', '&type=main', '', 0, 2, 1),
(275, 'Logo设置', 151, 'setting', 'index', '&type=other', '', 0, 3, 1),
(277, '商品管理', 52, 'items', 'index', '', '', 0, 1, 1),
(278, '文章管理', 155, 'article', 'index', '', '', 0, 1, 1),
(282, 'SEO设置', 2, 'seo', 'url', '', '', 0, 2, 1),
(283, 'URL静态化', 282, 'seo', 'url', '', '', 0, 255, 1),
(284, '页面SEO', 282, 'seo', 'page', '', '', 0, 255, 1),
(292, '商品分类', 50, 'itemscate', 'index', '', '', 0, 3, 1),
(305, '过期商品', 51, 'items', 'outtime', '', '', 0, 3, 1),
(307, '一键延时', 51, 'items', 'key_addtime', '', '', 0, 5, 1),
(249, '添加商品', 51, 'items', 'add', '', '', 0, 1, 1),
(323, '商品分类', 249, 'itemscate', 'ajax_getchilds', '', '', 0, 255, 0),
(324, '一键获取商品', 249, 'items', 'ajaxgetid', '', '', 0, 255, 1),
(328, '升级数据库', 31, 'backup', 'upsql', '', '', 0, 255, 1),
(331, '工具', 0, 'plugin', 'index', '', '', 0, 7, 1),
(338, '其他设置', 1, 'plugin', 'Link', '', '', 0, 255, 1),
(295, '帮助文件', 338, 'help', 'index', '', '', 0, 1, 1),
(12, '广告管理', 151, 'ad', 'index', '', '', 0, 4, 1),
(23, '新增', 12, 'ad', 'add', '', '', 0, 0, 0),
(24, '编辑', 12, 'ad', 'edit', '', '', 0, 0, 0),
(25, '删除', 12, 'ad', 'delete', '', '', 0, 0, 0),
(348, '内容管理', 0, 'article', 'index', '', '', 0, 255, 1),
(349, '文章管理', 348, 'article', 'index', '', '', 0, 255, 1),
(350, '文章列表', 349, 'article', 'index', '', '', 0, 255, 1),
(351, '文章分类', 349, 'articlecate', 'index', '', '', 0, 255, 1),
(359, '公众号直播管理', 331, 'zhibo', 'index', '', '', 0, 255, 1),
(356, '用户管理', 0, 'user', 'index', '', '', 0, 255, 1),
(357, '用户列表', 356, 'user', 'index', '', '', 0, 255, 1),
(358, '用户管理', 357, 'user', 'index', '', '', 0, 255, 1),
(360, '直播配置', 359, 'zhibo', 'setting', '', '', 0, 255, 1),
(363, '财务管理', 356, 'charge', 'index', '', '', 0, 255, 1),
(364, '余额提现', 363, 'balance', 'index', '', '', 0, 255, 1),
(365, '财务日志', 363, 'cash', 'index', '', '', 0, 255, 1),
(366, '订单管理', 363, 'order', 'index', '', '', 0, 255, 1),
(367, '站长申请', 357, 'apply', 'index', '', '', 0, 255, 1),
(368, '晒单管理', 348, 'basklist', 'index', '', '', 0, 255, 1),
(369, '晒单列表', 368, 'basklist', 'index', '', '', 0, 255, 1),
(370, '站长分成', 363, 'finance', 'flist', '', '', 0, 255, 1),
(371, '积分日志', 368, 'basklist', 'logs', '', '', 0, 255, 1),
(372, '品牌专场', 50, 'brand', 'index', '', '', 0, 255, 1),
(373, '品牌列表', 372, 'brand', 'index', '', '', 0, 255, 1),
(374, '品牌分类', 372, 'brandcate', 'index', '', '', 0, 255, 1),
(383, '百度链接提交', 331, 'tuisong', 'index', '', '', 0, 255, 1),
(384, '一键推送', 383, 'tuisong', 'index', '', '', 0, 255, 1),
(385, '拼多多商品管理', 51, 'pdditems', 'index', '', '', 0, 255, 1),
(386, '采集管理', 50, 'collect', 'index', '', '', 0, 255, 1),
(387, '采集列表', 386, 'robots', 'index', '', '', 0, 255, 1);



DROP TABLE IF EXISTS `tqk_robots`;
CREATE TABLE IF NOT EXISTS `tqk_robots` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `keyword` varchar(200) DEFAULT NULL,
  `cid` int(11) NOT NULL DEFAULT '16',
  `cate_id` int(11) DEFAULT '0' COMMENT '所属分类',
  `shop_type` varchar(50) NOT NULL DEFAULT 'all',
  `start_coupon_rate` int(11) NOT NULL DEFAULT '100',
  `end_coupon_rate` int(11) NOT NULL DEFAULT '10000',
  `start_commissionRate` int(11) NOT NULL DEFAULT '1000',
  `end_commissionRate` int(11) NOT NULL DEFAULT '9000',
  `start_credit` varchar(50) NOT NULL DEFAULT '1heart',
  `end_credit` varchar(50) NOT NULL DEFAULT '5goldencrown',
  `start_price` decimal(8,2) DEFAULT '0.00',
  `end_price` decimal(8,2) DEFAULT '999.00',
  `start_volume` int(11) NOT NULL DEFAULT '0',
  `end_volume` int(11) NOT NULL DEFAULT '999999',
  `ems` int(11) DEFAULT '0' COMMENT '采集包邮',
  `recid` int(11) DEFAULT '1' COMMENT '是否更新分类',
  `page` int(11) NOT NULL DEFAULT '100' COMMENT '采集页数',
  `ordid` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `status` int(11) DEFAULT '1',
  `sort` varchar(50) NOT NULL DEFAULT 'total_sales_des',
  `last_page` int(11) DEFAULT '0' COMMENT '上次采集页数',
  `last_time` int(11) DEFAULT NULL,
  `http_mode` int(11) DEFAULT '0' COMMENT '采集模式',
  `tb_cid` int(11) DEFAULT NULL COMMENT '淘宝网分类ID'
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_robots`
--
INSERT INTO `tqk_robots` (`id`, `name`, `keyword`, `cid`, `cate_id`, `recid`, `page`, `ordid`, `status`, `sort`, `last_page`, `last_time`, `http_mode`, `tb_cid`) VALUES
(11, '女装', '16,50000852,1629', 16, 1, 1, 500, 1, 1, '0', 4, 1527745432, 0, NULL),
(12, '男装', '30', 16, 2, 1, 500, 2, 1, '0', 500, 1527741146, 0, NULL),
(16, '美食', '50013061,50012981,50008613,50010550,50008055,50025682,50025689,50010696', 16, 8, 1, 500, 9, 1, '0', 1, 1527740338, 0, NULL),
(18, '居家', '50012051,50012791,50024797,50010103,213002,50017143,50009521,50012448', 16, 12, 1, 500, 7, 1, '0', 32, 1527740839, 0, NULL),
(20, '鞋包', '50006843,50026312,50012043,50011740,50012029,50012404,50012018,50010406,50014495,50006842', 16, 3, 1, 500, 3, 1, '0', 1, 1527735754, 0, NULL),
(22, '美妆', '50010815,50010793,50013794,50010803,50010789,50010790,50010792,50010798,50010808,50010810', 16, 6, 1, 500, 5, 1, '0', 1, 1527655249, 0, NULL),
(26, '内衣', '1625,50012433', 16, 9, 1, 500, 10, 1, '0', 1, 1527740375, 0, NULL),
(30, '运动', '50016663,50019502,50014023,50013888,50019539,50014762,50013228,50011717,50022728', 16, 16, 1, 500, 11, 1, '0', 12, 1527559072, 0, NULL),
(31, '数码', '1402,50002415,110508,110808,50018326,50024094,50018909,50009211,50012165,50012166', 16, 10, 1, 500, 12, 1, '0', 23, 1527559750, 0, NULL),
(32, '母婴', '211104,50018813,50009522,50013866,50014248,50022520, 50012314,50018394', 16, 7, 1, 500, 13, 1, '0', 68, 1527744083, 0, NULL),
(39, '文体车品', '50014480,50014482,50020835,50020856,50014481,50020841,50020836,50005757,211707,50011556', 16, 16, 1, 500, 255, 1, '0', 6, 1527743870, 0, NULL);


DROP TABLE IF EXISTS `tqk_itemscate`;
CREATE TABLE IF NOT EXISTS `tqk_itemscate` (
  `id` smallint(4) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `ali_id` varchar(50) DEFAULT NULL,
  `tags` varchar(50) NOT NULL,
  `pid` smallint(4) unsigned NOT NULL,
  `spid` varchar(50) NOT NULL,
  `remark` text NOT NULL,
  `sort` varchar(50) NOT NULL DEFAULT 'id  desc',
  `wait_time` varchar(50) NOT NULL DEFAULT '0',
  `end_time` varchar(50) NOT NULL DEFAULT '0',
  `shop_type` varchar(11) NOT NULL COMMENT 'B:商城C:集市',
  `mix_price` decimal(11,2) DEFAULT NULL,
  `max_price` decimal(11,2) DEFAULT NULL,
  `mix_volume` int(11) DEFAULT NULL,
  `max_volume` int(11) DEFAULT NULL,
  `ems` varchar(50) NOT NULL DEFAULT '0',
  `thiscid` int(11) NOT NULL DEFAULT '0' COMMENT '是否当前分类',
  `add_time` int(10) NOT NULL,
  `ordid` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `status` tinyint(1) NOT NULL,
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL,
  `cateimg` varchar(50) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=329 DEFAULT CHARSET=utf8;
INSERT INTO `tqk_itemscate` (`id`, `name`, `ali_id`, `tags`, `pid`, `spid`, `remark`, `sort`, `wait_time`, `end_time`, `shop_type`, `mix_price`, `max_price`, `mix_volume`, `max_volume`, `ems`, `thiscid`, `add_time`, `ordid`, `status`, `seo_title`, `seo_keys`, `seo_desc`, `cateimg`) VALUES
(1, '女装', '1', '1', 0, '0', 'FASHION LADIES', 'volume desc', '0', '0', '1', '0.00', '0.00', 0, 0, '0', 0, 0, 1, 1, '4333', '34342323', '44442342333', '1'),
(2, '男装', '2', '', 0, '0', 'Boutique men', 'volume desc', '0', '0', '', '0.00', '0.00', 0, 0, '0', 0, 0, 2, 1, '', '', '', 'icon-nanzhuang'),
(3, '鞋包', '3', '', 0, '0', 'Shoe accessories', 'volume desc', '0', '0', '', '0.00', '0.00', 0, 0, '0', 0, 0, 3, 1, '3', '', '', 'icon-xiemaoxiangbao'),
(6, '美妆', '6', '', 0, '0', 'Beauty care', 'volume desc', '0', '0', '', '0.00', '0.00', 0, 0, '0', 0, 0, 4, 1, '', '', '', 'icon-meizhuang'),
(7, '母婴', '7', '', 0, '0', 'Muyingpin', 'volume desc', '0', '0', '', '0.00', '0.00', 0, 0, '0', 0, 0, 5, 1, '', '', '', 'icon-muying01'),
(8, '食品', '8', '', 0, '0', 'Gourmet snacks', 'volume desc', '0', '0', '', '0.00', '0.00', 0, 0, '0', 0, 0, 6, 1, '', '', '', 'icon-xiuxian'),
(9, '内衣', '9', '', 0, '0', 'Underwear tights', 'volume desc', '0', '0', '', '0.00', '0.00', 0, 0, '0', 0, 0, 7, 1, '', '', '', 'icon-neiyi'),
(10, '数码', '10', '', 0, '0', 'Digital home', 'volume desc', '0', '0', '', '0.00', '0.00', 0, 0, '0', 0, 0, 8, 1, '', '', '', 'icon-shuma'),
(12, '家居用品', '12', '', 0, '0', 'Housewear', 'volume desc', '0', '0', '', '0.00', '0.00', 0, 0, '0', 0, 0, 14, 1, '', '', '', 'icon-jiaju'),
(16, '文体车品', '16', '', 0, '0', 'Automobile', 'volume desc', '0', '0', '', '0.00', '0.00', 0, 0, '0', 0, 0, 13, 1, '', '', '', 'icon-wenhua'),
(151, '连衣裙', '50010850', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i4/2053469401/O1CN01Tevn7I2JJhtfK39LK-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572223836, 255, 1, '', '', '', NULL),
(152, '卫衣', '50008898', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN012JJhsmFofvZn9Qi-2053469401.jpg', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572223862, 255, 1, '', '', '', NULL),
(316, '套装', '1624', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2wcqxtDlYBeNjSszcXXbwhFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574320308, 255, 1, '', '', '', '1'),
(315, '裤子', '162201', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN01PhRMnX2JJhyyyjVbF_!!2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574320272, 255, 1, '', '', '', '1'),
(160, '长外套', '50008901', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i4/2053469401/O1CN01qFwB662JJhutoLjt8-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572224053, 255, 1, '', '', '', NULL),
(161, '中老年女装', '50000852', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN01k8lIxi2JJhtRa60Db-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572224078, 255, 1, '', '', '', NULL),
(162, '半身裙', '1623', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i1/2053469401/O1CN01JMFIvX2JJhtfK3U8Q-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572224101, 255, 1, '', '', '', NULL),
(163, '牛仔裤', '162205', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2zD.LtXmWBuNjSspdXXbugXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572224126, 255, 1, '', '', '', NULL),
(319, '打底衫', '162116', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2CcCCvMmTBuNjy1XbXXaMrVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574326484, 255, 1, '', '', '', '1'),
(314, '衬衫', '162104', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN01ZMuzzi2JJhyzhiY1Z_!!2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574320234, 255, 1, '', '', '', '1'),
(167, '方便速食', '50025689', '1', 8, '8|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2gO5rtr1YBuNjSszhXXcUsFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572224227, 255, 1, '', '', '', NULL),
(168, '糕点饼干', '50010513', '1', 8, '8|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2cxKstA9WBuNjSspeXXaz5VXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572224248, 255, 1, '', '', '', NULL),
(170, '瘦身美容', '50005003', '1', 8, '8|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2mPVSwrGYBuNjy0FoXXciBFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572224399, 255, 1, '', '', '', NULL),
(327, '咖啡', '210605', '1', 8, '8|', 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN01jXKUGz2JJhtg60WVn-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574327983, 255, 1, '', '', '', '1'),
(326, '糖果巧克力', '124312003', '1', 8, '8|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2FHfDwCtYBeNjSspkXXbU8VXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574327926, 255, 1, '', '', '', '1'),
(325, '坚果果干', '126472004', '1', 8, '8|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2LQ9utx1YBuNjy1zcXXbNcXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574327891, 255, 1, '', '', '', '1'),
(324, '辣条', '126492001', '1', 8, '8|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2oyQrtDJYBeNjy1zeXXahzVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574327845, 255, 1, '', '', '', '1'),
(323, '零食大礼包', '124308001', '1', 8, '8|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2RXqituSSBuNjy0FlXXbBpVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574327775, 255, 1, '', '', '', '1'),
(322, '火锅', '50009822', '1', 8, '8|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2aHN8trGYBuNjy0FoXXciBFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574327742, 255, 1, '', '', '', '1'),
(321, '营养早餐', '50009860', '1', 8, '8|', 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN01LJQwqs2JJhumFVvGX-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574327722, 255, 1, '', '', '', '1'),
(318, '短外套', '50011277', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i1/2053469401/O1CN01Kf85St2JJhutCPY0J-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574326369, 255, 1, '', '', '', '1'),
(184, '卸妆洁面', '50011977', '1', 6, '6|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2X61HtxSYBuNjSspjXXX73VXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572224955, 255, 1, '', '', '', NULL),
(185, '隔离防晒', '50011982', '1', 6, '6|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2oKRdwpmWBuNjSspdXXbugXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572224983, 255, 1, '', '', '', NULL),
(320, '棉衣/棉服', '50008900', '1', 1, '1|', 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN01Z8HerC2JJhtfK4DoP-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1574326989, 255, 1, '', '', '', '1'),
(188, '遮瑕修容', '121382014', '1', 6, '6|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB20YQKk2iSBuNkSnhJXXbDcpXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225086, 255, 1, '', '', '', NULL),
(189, '粉饼散粉', '50010790', '1', 6, '6|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN01AKIClx2JJhusTb1y2-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225115, 255, 1, '', '', '', NULL),
(190, '护肤套装', '50011993', '1', 6, '6|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2ga6OtCtYBeNjSspkXXbU8VXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225149, 255, 1, '', '', '', NULL),
(192, '眉妆', '50010798', '1', 6, '6|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2uF0llndYBeNkSmLyXXXfnVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225198, 255, 1, '', '', '', NULL),
(195, '沐浴用品', '121370009', '1', 6, '6|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2DljVvH9YBuNjy0FgXXcxcXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225267, 255, 1, '', '', '', NULL),
(197, '身体护理', '50456019', '1', 6, '6|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2DljVvH9YBuNjy0FgXXcxcXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225310, 255, 1, '', '', '', NULL),
(199, '隐形眼镜', '50011891', '1', 6, '6|', 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN01Lp8zbd2JJhuqn1liM-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225352, 255, 1, '', '', '', NULL),
(200, '香氛精油', '216505', '1', 6, '6|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2uEk6kZuYBuNkSmRyXXcA3pXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225376, 255, 1, '', '', '', NULL),
(201, '纸品湿巾', '50010895', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2blM6kZuYBuNkSmRyXXcA3pXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225444, 255, 1, '', '', '', NULL),
(204, '洗漱用品', '50015985', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN01sCvaw62JJhtjF24OK-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225520, 255, 1, '', '', '', NULL),
(206, '洗衣液', '50012466', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB23G1wtpuWBuNjSspnXXX1NVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225564, 255, 1, '', '', '', NULL),
(208, '厨卫清洁', '124222014', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i4/2053469401/O1CN0113jWSa2JJhtjQCyIO-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225605, 255, 1, '', '', '', NULL),
(209, '衣物晾晒', '50005055', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2yjWgtpuWBuNjSszbXXcS7FXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225625, 255, 1, '', '', '', NULL),
(210, '绿植园艺', '50007216', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2Zp8qliOYBuNjSsD4XXbSkFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225649, 255, 1, '', '', '', NULL),
(212, '萌宠用品', '121466037', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2fO9jtruWBuNjSszgXXb8jVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225773, 255, 1, '', '', '', NULL),
(215, 'T恤', '50000436', '1', 2, '2|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN01cxIwuT2JJhtOi7gVm-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225845, 255, 1, '', '', '', '1'),
(217, 'Polo衫', '50010402', '1', 2, '2|', 'https://img.alicdn.com/imgextra/i4/2053469401/O1CN01JxP3OG2JJhupZp8ZF-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225893, 255, 1, '', '', '', NULL),
(220, '夹克', '50010158', '1', 2, '2|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB275FwlnXYBeNkHFrdXXciuVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225953, 255, 1, '', '', '', '1'),
(221, '针织衫', '50000557', '1', 2, '2|', 'https://img.alicdn.com/imgextra/i4/2053469401/O1CN012JJhseJguVFeEOr-2053469401.jpg', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572225973, 255, 1, '', '', '', NULL),
(222, '休闲裤', '3035', '1', 2, '2|', 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN01Ppnqmt2JJhtesIIt2-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226014, 255, 1, '', '', '', NULL),
(224, '运动裤', '50023105', '1', 2, '2|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN01HFCVpr2JJhtQXljIe-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226063, 255, 1, '', '', '', NULL),
(226, '商务装', '50011130', '1', 2, '2|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2ziGqtr1YBuNjSszhXXcUsFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226109, 255, 1, '', '', '', NULL),
(228, '跑鞋', '50026394', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i2/2053469401/O1CN01i4RWkd2JJhuqmzHqP-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226183, 255, 1, '', '', '', NULL),
(229, '运动鞋', '50012029', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2VRBUtAKWBuNjy1zjXXcOypXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226206, 255, 1, '', '', '', NULL),
(230, '休闲鞋', '50012043', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2kp5YtrSYBuNjSspfXXcZCpXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226239, 255, 1, '', '', '', NULL),
(231, '帆布鞋', '50011744', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2jApBlm8YBeNkSnb4XXaevFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226264, 255, 1, '', '', '', NULL),
(235, '拖鞋', '50008340', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2Z36ivHGYBuNjy0FoXXciBFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226352, 255, 1, '', '', '', NULL),
(237, '女鞋', '50006843', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i1/2053469401/O1CN01vz3WCr2JJhursIvXj-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226396, 255, 1, '', '', '', NULL),
(238, '男鞋', '50011740', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN01ZeStzq2JJhutLI2sv-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226421, 255, 1, '', '', '', NULL),
(239, '耳机', '1205', '1', 10, '10|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2AWDVk5CYBuNkHFCcXXcHtVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226452, 255, 1, '', '', '', NULL),
(241, '数据线', '50003327', '1', 10, '10|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2ANVUtAKWBuNjy1zjXXcOypXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226495, 255, 1, '', '', '', NULL),
(242, '充电宝', '50009211', '1', 10, '10|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2r8z3tCtYBeNjSspaXXaOOFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226523, 255, 1, '', '', '', NULL),
(243, '电脑外设', '110210', '1', 10, '10|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN014Eh50x2JJhtiIjCkG-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226569, 255, 1, '', '', '', '1'),
(244, '厨房家电', '50002900', '1', 10, '10|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2cGBDwpGWBuNjy0FbXXb4sXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226590, 255, 1, '', '', '', NULL),
(245, '日用小家电', '50013008', '1', 10, '10|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN01o1sN142JJhtiEnzDa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226610, 255, 1, '', '', '', NULL),
(246, '剃须刀', '350201', '1', 10, '10|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2QBg.n2uSBuNkHFqDXXXfhVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226645, 255, 1, '', '', '', NULL),
(247, '电吹风', '350213', '1', 10, '10|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2LoQKk8yWBuNkSmFPXXXguVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226702, 255, 1, '', '', '', NULL),
(248, '手机支架', '150708', '1', 10, '10|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2I6yltuGSBuNjSspbXXciipXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226723, 255, 1, '', '', '', NULL),
(250, '加湿器', '350407', '1', 10, '10|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN01QNge1u2JJhtijqvst-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226766, 255, 1, '', '', '', NULL),
(251, '婴童服饰', '50010537', '1', 7, '7|', 'https://img.alicdn.com/imgextra/i4/2053469401/O1CN01xW99ck2JJhthg0TNO-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226789, 255, 1, '', '', '', NULL),
(252, '婴童鞋靴', '50012345', '1', 7, '7|', 'https://img.alicdn.com/imgextra/i1/2053469401/O1CN01gouWdv2JJhthmMYU4-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226814, 255, 1, '', '', '', NULL),
(253, '玩具乐器', '50017321', '1', 7, '7|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2ODGutx1YBuNjy1zcXXbNcXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226839, 255, 1, '', '', '', NULL),
(254, '宝宝用品', '50009521', '1', 7, '7|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB20ZtYtpGWBuNjy0FbXXb4sXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226861, 255, 1, '', '', '', '1'),
(256, '尿不湿', '50018813', '1', 7, '7|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2tBaHtxSYBuNjSspjXXX73VXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226901, 255, 1, '', '', '', NULL),
(257, '奶瓶奶嘴', '50009522', '1', 7, '7|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2w8OotuuSBuNjSsziXXbq8pXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226922, 255, 1, '', '', '', NULL),
(258, '婴童湿巾', '50012546', '1', 7, '7|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB260KWtrSYBuNjSspfXXcZCpXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226944, 255, 1, '', '', '', NULL),
(259, '出行用品', '50005962', '1', 7, '7|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2ZuKxtr9YBuNjy0FgXXcxcXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226966, 255, 1, '', '', '', NULL),
(260, '斜挎包', '50012410', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2aw1knBmWBuNkSndVXXcsApXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572226990, 255, 1, '', '', '', '1'),
(261, '双肩包', '121388027', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2qqYatxWYBuNjy1zkXXXGGpXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227014, 255, 1, '', '', '', NULL),
(262, '旅行箱包', '121398019', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB29lWltuGSBuNjSspbXXciipXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227036, 255, 1, '', '', '', NULL),
(263, '手提包', '50012010', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2qnGFnyOYBuNjSsD4XXbSkFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227061, 255, 1, '', '', '', NULL),
(265, '帆布包', '50012075', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN01Lr9ij72JJhtiSwQ2M-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227107, 255, 1, '', '', '', NULL),
(266, '真皮包', '122654005', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB26wKstr1YBuNjSszhXXcUsFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227131, 255, 1, '', '', '', '1'),
(270, '车饰车品', '50018699', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2khNBlm8YBeNkSnb4XXaevFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227644, 255, 1, '', '', '', NULL),
(272, '书写工具', '50012716', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2SACltuGSBuNjSspbXXciipXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227688, 255, 1, '', '', '', NULL),
(275, '车载电器', '261710', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2wRCwtr5YBuNjSspoXXbeNFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227765, 255, 1, '', '', '', NULL),
(276, '艺术礼品', '50020835', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2pt1ttA9WBuNjSspeXXaz5VXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227785, 255, 1, '', '', '', NULL),
(277, '画具画材', '50012731', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2LgtmonXYBeNkHFrdXXciuVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227805, 255, 1, '', '', '', NULL),
(278, '耳饰项链', '50013865', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB280Kntv9TBuNjy1zbXXXpepXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227835, 255, 1, '', '', '', NULL),
(279, '手链戒指', '50013875', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB22hOXtxSYBuNjSsphXXbGvVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227858, 255, 1, '', '', '', NULL),
(280, '手表', '50014781', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2TsEstDJYBeNjy1zeXXahzVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227900, 255, 1, '', '', '', NULL),
(281, '发饰', '50013878', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB236jun5CYBuNkHFCcXXcHtVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227919, 255, 1, '', '', '', '1'),
(282, '眼镜墨镜', '50010368', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB21ygJuFGWBuNjy0FbXXb4sXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227940, 255, 1, '', '', '', NULL),
(283, '帽子', '302910', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2SJGwtACWBuNjy0FaXXXUlXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227962, 255, 1, '', '', '', NULL),
(284, '皮带', '50009032', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2RAPwtCBYBeNjy0FeXXbnmFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572227988, 255, 1, '', '', '', NULL),
(285, '纱巾', '50007003', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN010lR9er2JJhunNL5f9-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572228010, 255, 1, '', '', '', NULL),
(286, '其他饰品', '50013869', '1', 3, '3|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2vlF8trGYBuNjy0FoXXciBFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572228032, 255, 1, '', '', '', NULL),
(287, '文胸', '50008881', '1', 9, '9|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2YQuxtr9YBuNjy0FgXXcxcXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572228060, 255, 1, '', '', '', NULL),
(288, '内裤', '50008882', '1', 9, '9|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2OyP3tCtYBeNjSspaXXaOOFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572228084, 255, 1, '', '', '', NULL),
(289, '睡衣/睡袍', '50012773', '1', 9, '9|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB23CCrtAyWBuNjy0FpXXassXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572228105, 255, 1, '', '', '', NULL),
(290, '袜子', '50006122', '1', 9, '9|', 'https://img.alicdn.com/imgextra/i4/2053469401/O1CN01lKaoV32JJhtjVMfCH-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572228940, 255, 1, '', '', '', NULL),
(291, '打底裤/丝袜', '50006846', '1', 9, '9|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2vvdUtAKWBuNjy1zjXXcOypXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572228963, 255, 1, '', '', '', NULL),
(292, '吊带背心', '121412004', '1', 9, '9|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2jkCwtpuWBuNjSspnXXX1NVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572228982, 255, 1, '', '', '', NULL),
(293, '塑身衣', '50023660', '1', 9, '9|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2Q14Gtv9TBuNjy0FcXXbeiFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572229007, 255, 1, '', '', '', NULL),
(294, '情侣睡衣', '50012772', '1', 9, '9|', 'https://img.alicdn.com/imgextra/i1/2053469401/O1CN01aFlGam2JJhtQv137d-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572229028, 255, 1, '', '', '', NULL),
(296, '枕头枕芯', '50002777', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i1/2053469401/O1CN01NAlSrl2JJhutCsTWm-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572231523, 255, 1, '', '', '', NULL),
(297, '靠枕抱枕', '213002', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2ajBPtrSYBuNjSspiXXXNzpXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572231627, 255, 1, '', '', '', NULL),
(298, '装饰品', '50018914', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB29kOetv5TBuNjSspcXXbnGFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572231648, 255, 1, '', '', '', NULL),
(299, '地垫地毯', '50000582', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i4/2053469401/O1CN01SXSQ2j2JJhtgWqFWp-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572231669, 255, 1, '', '', '', NULL),
(300, '家具家装', '50020002', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB2OCahtuSSBuNjy0FlXXbBpVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572231697, 255, 1, '', '', '', NULL),
(301, '墙纸贴饰', '50024688', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2vxN6wuSSBuNjy0FlXXbBpVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572232185, 255, 1, '', '', '', NULL),
(302, '灯具', '50008698', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i3/2053469401/TB2.roPk4uTBuNkHFNRXXc9qpXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572232207, 255, 1, '', '', '', NULL),
(303, '卫浴用品', '50020007', '1', 12, '12|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB251ratv1TBuNjy0FjXXajyXXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572232229, 255, 1, '', '', '', NULL),
(305, '健身装备', '121410016', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB27Bmltv9TBuNjy1zbXXXpepXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572232284, 255, 1, '', '', '', NULL),
(306, '健身服装', '50022891', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i4/2053469401/O1CN01gC8PTH2JJhtfU3OD4-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572232308, 255, 1, '', '', '', NULL),
(307, '户外装备', '50014822', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2jEdstwmTBuNjy1XbXXaMrVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572232331, 255, 1, '', '', '', NULL),
(308, '文体用品', '50010728', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i3/2053469401/O1CN01uFPwnB2JJhtnu91Tt-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572232351, 255, 1, '', '', '', '1'),
(309, '游泳装备', '50016748', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i2/2053469401/TB2.W1vtr5YBuNjSspoXXbeNFXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572232374, 255, 1, '', '', '', NULL),
(310, '瑜伽垫', '50016665', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i4/2053469401/TB2rSl_tuuSBuNjSsplXXbe8pXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572232394, 255, 1, '', '', '', NULL),
(311, '瑜伽用品', '50016677', '1', 16, '16|', 'https://img.alicdn.com/imgextra/i1/2053469401/TB21gaMtER1BeNjy0FmXXb0wVXa-2053469401.png', 'id  desc', '0', '0', '1', NULL, NULL, NULL, NULL, '0', 0, 1572232420, 255, 1, '', '', '', NULL);
ALTER TABLE `tqk_itemscate` ADD PRIMARY KEY (`id`);
ALTER TABLE `tqk_itemscate` MODIFY `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=329;

--
-- Indexes for dumped tables
--
--
-- 表的结构 `tqk_pddorder`
--

CREATE TABLE IF NOT EXISTS `tqk_pddorder` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(50) DEFAULT NULL,
  `order_amount` varchar(20) DEFAULT NULL,
  `promotion_amount` varchar(10) DEFAULT NULL,
  `order_status` varchar(10) DEFAULT NULL,
  `p_id` varchar(20) DEFAULT NULL,
  `order_pay_time` varchar(16) DEFAULT NULL,
  `order_settle_time` varchar(16) DEFAULT NULL,
  `uid` int(15) DEFAULT NULL,
  `goods_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='拼多多订单表' AUTO_INCREMENT=22 ;
--
-- Indexes for table `tqk_menu`
--
ALTER TABLE `tqk_menu`
  ADD PRIMARY KEY (`id`);
  
--
-- AUTO_INCREMENT for dumped tables
--
ALTER TABLE `tqk_article` ADD `is_xz` TINYINT(1) NOT NULL DEFAULT '0' AFTER `pic`;
--
-- AUTO_INCREMENT for table `tqk_menu`
--
ALTER TABLE `tqk_menu`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=385;
ALTER TABLE  `tqk_user` ADD  `fuid` INT( 12 ) NULL ,ADD  `guid` INT( 12 ) NULL, ADD  `invocode` VARCHAR( 30 ) NULL ; 
ALTER TABLE  `tqk_order` ADD  `fuid` INT( 12 ) NULL ,
ADD  `guid` INT( 12 ) NULL ,
ADD  `leve1` INT( 3 ) NULL DEFAULT  '0',
ADD  `leve2` INT( 3 ) NULL DEFAULT  '0',
ADD  `leve3` INT( 3 ) NULL DEFAULT  '0',
ADD  `nstatus` TINYINT( 1 ) NULL DEFAULT  '0';
INSERT INTO  `tqk_setting` (
`name` ,
`data` ,
`remark`
)
VALUES (
'bili1',  '',  ''
), (
'bili2',  '',  ''
),(
'bili3',  '',  ''
),(
'agentcondition',  '300',  ''
),(
'islogin',  '0',  ''
);

ALTER TABLE  `tqk_items` CHANGE  `pic_urls`  `pic_urls` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  '宽版图片';
INSERT INTO  `tqk_setting` (
`name` ,
`data` ,
`remark`
)
VALUES (
'Quota',  '',  ''
), (
'ComputingTime',  '',  ''
), (
'invocode',  '1',  ''
), (
'qrcode',  '0',  ''
),  (
'reinte',  '0',  ''
),  (
'isfanli',  '1',  ''
),  (
'openduoduo',  '1',  ''
);
ALTER TABLE  `tqk_user` ADD  `realname` VARCHAR( 30 ) NULL ,ADD  `alipay` VARCHAR( 40 ) NULL ;
ALTER TABLE  `tqk_items` CHANGE  `quan`  `quan` INT( 10 ) NOT NULL ;
ALTER TABLE  `tqk_items` CHANGE  `sellerId`  `sellerId` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `tqk_order` ADD `relation_id` VARCHAR(30) NULL AFTER `leve3`, ADD `special_id` VARCHAR(30) NULL AFTER `relation_id`, ADD `bask` TINYINT(1) NULL DEFAULT '0' AFTER `special_id`;
UPDATE `tqk_menu` SET `name` = '代理分成' WHERE `tqk_menu`.`id` = 370;
UPDATE `tqk_menu` SET `pid` = '363' WHERE `tqk_menu`.`id` = 371;
ALTER TABLE `tqk_user` ADD `relation_id` VARCHAR(30) NULL AFTER `alipay`, ADD `special_id` VARCHAR(30) NULL AFTER `relation_id`;
ALTER TABLE `tqk_items` CHANGE `sellerId` `sellerId` VARCHAR(21) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
UPDATE `tqk_setting` SET `data` = '￥|￥' WHERE `tqk_setting`.`name` = 'dingdan';
UPDATE `tqk_menu` SET `module_name` = 'banner' WHERE `tqk_menu`.`id` = 12;
UPDATE `tqk_menu` SET `module_name` = 'banner' WHERE `tqk_menu`.`id` = 23;
UPDATE `tqk_menu` SET `module_name` = 'banner' WHERE `tqk_menu`.`id` = 24;
UPDATE `tqk_menu` SET `module_name` = 'banner' WHERE `tqk_menu`.`id` = 25;
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('sms_status', '0', ''), ('sms_appid', '', ''), ('sms_appkey', '', ''), ('sms_sign', '', ''), ('sms_reg_id', '', ''), ('sms_fwd_id', '', ''), ('sms_my_phone', '', ''), ('sms_tixian_id', '', ''), ('api_line', 'http://api.tuiquanke.cn', ''), ('bingtaobao', '0', '');
ALTER TABLE `tqk_itemscate` MODIFY `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=329;
ALTER TABLE `tqk_order` ADD `settle` TINYINT( 1 ) NULL DEFAULT '0';
UPDATE `tqk_order` SET `settle`=1  WHERE `up_time`< 1572537600 AND `status` = 3;
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('invitecode', '', '');
ALTER  TABLE  `tqk_items`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_items`  ADD  INDEX idx_ordid (`ordid`);
ALTER  TABLE  `tqk_brand`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_brand`  ADD  INDEX idx_ordid (`ordid`);
ALTER  TABLE  `tqk_brand`  ADD  INDEX idx_cate_id (`cate_id`);
ALTER  TABLE  `tqk_brandcate`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_items`  ADD  INDEX idx_is_commend (`is_commend`);
ALTER  TABLE  `tqk_pdditems`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_order`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_user`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_usercash`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_order`  ADD  INDEX idx_oid (`oid`);
ALTER  TABLE  `tqk_user`  ADD  INDEX idx_oid (`oid`);
ALTER  TABLE  `tqk_order`  ADD  INDEX idx_relation_id (`relation_id`);
ALTER  TABLE  `tqk_user`  ADD  INDEX idx_webmaster_pid (`webmaster_pid`);
ALTER TABLE `tqk_user` ADD `tb_refresh_token` VARCHAR( 255 ) NULL ,
ADD `tb_ expire_time` VARCHAR( 30 ) NULL ,
ADD `tb_open_uid` VARCHAR( 100 ) NULL ,
ADD `tb_ access_token` VARCHAR( 255 ) NULL ;
ALTER TABLE `tqk_article` CHANGE `ordid` `ordid` MEDIUMINT( 3 ) UNSIGNED NOT NULL DEFAULT '255';
ALTER  TABLE  `tqk_article`  ADD  INDEX idx_ordid (`ordid`);
ALTER  TABLE  `tqk_pdditems`  ADD  INDEX idx_orderid (`orderid`);
ALTER  TABLE  `tqk_items`  ADD  INDEX idx_ems (`ems`);
ALTER  TABLE  `tqk_items`  ADD  INDEX idx_volume (`volume`);
ALTER  TABLE  `tqk_items`  ADD  INDEX idx_coupon_price (`coupon_price`);
ALTER  TABLE  `tqk_pdditems`  ADD  INDEX idx_addtime (`addtime`);
ALTER  TABLE  `tqk_pdditems`  ADD  INDEX idx_min_group_price (`min_group_price`);
ALTER  TABLE  `tqk_pdditems`  ADD  INDEX idx_coupon_discount (`coupon_discount`);
ALTER  TABLE  `tqk_pdditems`  ADD  INDEX idx_sold_quantity (`sold_quantity`);
ALTER TABLE `tqk_pdditems` CHANGE `coupon_discount` `coupon_discount` DECIMAL( 10 ) NULL ;
ALTER TABLE `tqk_ad` CHANGE `url` `url` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;
DROP TABLE IF EXISTS `tqk_jditems`;
CREATE TABLE `tqk_jditems` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `commission_rate` decimal(10,2) DEFAULT NULL COMMENT '佣金比例',
  `quan` decimal(10,0) DEFAULT NULL COMMENT '券面额',
  `couponlink` varchar(200) DEFAULT NULL COMMENT '券链接',
  `pict` varchar(120) DEFAULT NULL COMMENT '图片主图',
  `itemurl` varchar(120) DEFAULT NULL COMMENT '商品落地页',
  `coupon_price` decimal(10,2) DEFAULT NULL COMMENT '最低价后的优惠券价',
  `price` decimal(10,2) DEFAULT NULL COMMENT '无线价格',
  `owner` varchar(2) DEFAULT NULL COMMENT '是否自营',
  `comments` int(15) DEFAULT NULL COMMENT '评论数',
  `cate_id` int(5) DEFAULT NULL COMMENT '分类',
  `itemid` varchar(30) DEFAULT NULL COMMENT '商品ID',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `item_type` tinyint(1) DEFAULT '1' COMMENT '商品类型',
  `start_time` varchar(30) DEFAULT NULL COMMENT '开始时间',
  `end_time` varchar(30) DEFAULT NULL COMMENT '结束时间',
  `cid2` int(5) DEFAULT NULL COMMENT '子分类ID',
  `add_time` varchar(20) DEFAULT NULL COMMENT '添加时间',
  `click_url` text COMMENT '推广链接',
  `shop_name` varchar(50) DEFAULT NULL COMMENT '店铺名',
  `shop_level` varchar(3) DEFAULT NULL COMMENT '店铺评分',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_itemid` (`itemid`) USING BTREE,
  KEY `idx_id` (`id`) USING BTREE,
  KEY `idx_ comments` (`comments`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
INSERT INTO `tqk_setting` ( `name` , `data` , `remark` )
VALUES (
'jdappkey', '', ''
), (
'jdsecretkey', '', ''
), (
'jdpid', '', ''
), (
'openjd', '', ''
);
DROP TABLE IF EXISTS `tqk_nav`;
CREATE TABLE `tqk_nav` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinytext,
  `name` varchar(50) NOT NULL,
  `alias` varchar(20) NOT NULL,
  `link` varchar(255) NOT NULL,
  `target` tinyint(1) NOT NULL DEFAULT '1',
  `ordid` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `mod` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
INSERT INTO `tqk_nav` (`id`, `type`, `name`, `alias`, `link`, `target`, `ordid`, `mod`, `status`) VALUES
(1, '0', '超级人气榜', 'top100', '/index.php/top100', 0, 2, '', 1),
(2, '0', '京东优惠券', 'jd', '/index.php/jd', 0, 5, '', 1),
(3, '0', '品牌优惠券', 'brand', '/index.php/brand', 0, 3, '', 1),
(4, '0', '9块9包邮', 'jiu', '/index.php/jiu', 0, 2, '', 1),
(6, '0', '优惠券头条', 'article', '/index.php/article', 0, 255, '', 1),
(8, '0', '拼多多券', 'pdd', '/index.php/pdd', 0, 6, '', 1),
(12, '1', '淘宝优惠券', 'topic', '/index.php/cate', 1, 1, 'alias', 1);
DROP TABLE IF EXISTS `tqk_menu`;
CREATE TABLE `tqk_menu` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `pid` smallint(6) NOT NULL,
  `module_name` varchar(20) NOT NULL,
  `action_name` varchar(20) NOT NULL,
  `data` varchar(120) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `often` tinyint(1) NOT NULL DEFAULT '0',
  `ordid` mediumint(3) unsigned NOT NULL DEFAULT '255',
  `display` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_ordid` (`ordid`),
  KEY `idx_id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
INSERT INTO `tqk_menu` (`id`, `name`, `pid`, `module_name`, `action_name`, `data`, `remark`, `often`, `ordid`, `display`) VALUES
(1, '网站管理', 0, 'setting', 'index', '', '', 0, 1, 1),
(2, '核心设置', 1, 'setting', 'index', '', '', 0, 1, 1),
(3, '首页参数', 151, 'setting', 'index', '&amp;type=site', '', 0, 1, 1),
(6, '菜单管理', 2, 'menu', 'index', '', '', 0, 4, 1),
(7, '新增', 6, 'menu', 'add', '', '', 0, 0, 0),
(8, '编辑', 6, 'menu', 'edit', '', '', 0, 0, 0),
(9, '删除', 6, 'menu', 'delete', '', '', 0, 0, 0),
(14, '友情链接', 288, 'link', 'index', '', '', 0, 2, 1),
(15, '友情链接', 338, 'link', 'index', '', '', 0, 0, 1),
(17, '新增', 15, 'link', 'add', '', '', 0, 0, 0),
(18, '编辑', 15, 'link', 'edit', '', '', 0, 0, 0),
(19, '删除', 15, 'link', 'delete', '', '', 0, 0, 0),
(31, '数据库管理', 331, 'backup', 'index', '', '', 0, 2, 1),
(32, '数据备份', 31, 'backup', 'index', '', '', 0, 0, 1),
(33, '数据恢复', 31, 'backup', 'restore', '', '', 0, 0, 1),
(34, '清理缓存', 2, 'cache', 'index', '', '', 1, 0, 0),
(50, '数据管理', 0, 'content', 'index', '', '', 0, 2, 1),
(51, '商品管理', 50, 'product', 'index', '', '', 0, 2, 1),
(52, '淘宝商品管理', 51, 'items', 'index', '', '', 0, 2, 1),
(54, '编辑', 52, 'article', 'edit', '', '', 0, 3, 0),
(56, '商品分类', 292, 'itemscate', 'index', '', '', 0, 6, 1),
(60, '管理员管理', 1, 'admin', 'index', '', '', 0, 3, 1),
(61, '管理员管理', 60, 'admin', 'index', '', '', 0, 1, 1),
(62, '新增', 61, 'admin', 'add', '', '', 0, 0, 0),
(63, '编辑', 61, 'admin', 'edit', '', '', 0, 0, 0),
(64, '删除', 61, 'admin', 'delete', '', '', 0, 0, 0),
(65, '分组管理', 60, 'adminrole', 'index', '', '', 0, 2, 1),
(66, '新增', 65, 'adminrole', 'add', '', '', 0, 0, 0),
(148, '站点设置', 2, 'setting', 'index', '', '', 0, 0, 1),
(150, '删除', 149, 'user', 'delete', '', '', 0, 5, 0),
(151, '首页设置', 1, 'nav', 'index', '', '', 0, 1, 1),
(152, '导航设置', 151, 'nav', 'index', '&type=main', '', 0, 2, 1),
(275, 'Logo设置', 151, 'setting', 'index', '&type=other', '', 0, 3, 1),
(277, '商品管理', 52, 'items', 'index', '', '', 0, 1, 1),
(278, '文章管理', 155, 'article', 'index', '', '', 0, 1, 1),
(282, 'SEO设置', 2, 'seo', 'url', '', '', 0, 2, 1),
(283, 'URL静态化', 282, 'seo', 'url', '', '', 0, 255, 1),
(284, '页面SEO', 282, 'seo', 'page', '', '', 0, 255, 1),
(292, '商品分类', 50, 'itemscate', 'index', '', '', 0, 3, 1),
(305, '京东商品管理', 51, 'jditems', 'index', '', '', 0, 3, 1),
(249, '添加淘宝商品', 51, 'items', 'add', '', '', 0, 1, 1),
(323, '商品分类', 249, 'itemscate', 'ajax_getchilds', '', '', 0, 255, 0),
(324, '一键获取商品', 249, 'items', 'ajaxgetid', '', '', 0, 255, 1),
(328, '升级数据库', 31, 'backup', 'upsql', '', '', 0, 255, 1),
(331, '工具', 0, 'plugin', 'index', '', '', 0, 7, 1),
(338, '其他设置', 1, 'plugin', 'Link', '', '', 0, 255, 1),
(295, '帮助文件', 338, 'help', 'index', '', '', 0, 1, 1),
(12, '广告管理', 151, 'banner', 'index', '', '', 0, 4, 1),
(23, '新增', 12, 'banner', 'add', '', '', 0, 0, 0),
(24, '编辑', 12, 'banner', 'edit', '', '', 0, 0, 0),
(25, '删除', 12, 'banner', 'delete', '', '', 0, 0, 0),
(348, '内容管理', 0, 'article', 'index', '', '', 0, 255, 1),
(349, '文章管理', 348, 'article', 'index', '', '', 0, 255, 1),
(350, '文章列表', 349, 'article', 'index', '', '', 0, 255, 1),
(351, '文章分类', 349, 'articlecate', 'index', '', '', 0, 255, 1),
(356, '用户管理', 0, 'user', 'index', '', '', 0, 255, 1),
(357, '用户列表', 356, 'user', 'index', '', '', 0, 255, 1),
(358, '用户管理', 357, 'user', 'index', '', '', 0, 255, 1),
(360, '直播配置', 359, 'zhibo', 'setting', '', '', 0, 255, 1),
(363, '财务管理', 356, 'charge', 'index', '', '', 0, 255, 1),
(364, '余额提现', 363, 'balance', 'index', '', '', 0, 255, 1),
(365, '财务日志', 363, 'cash', 'index', '', '', 0, 255, 1),
(366, '订单管理', 363, 'order', 'index', '', '', 0, 255, 1),
(368, '晒单管理', 348, 'basklist', 'index', '', '', 0, 255, 1),
(369, '晒单列表', 368, 'basklist', 'index', '', '', 0, 255, 1),
(371, '积分日志', 363, 'basklist', 'logs', '', '', 0, 255, 1),
(372, '品牌专场', 50, 'brand', 'index', '', '', 0, 255, 1),
(373, '品牌列表', 372, 'brand', 'index', '', '', 0, 255, 1),
(374, '品牌分类', 372, 'brandcate', 'index', '', '', 0, 255, 1),
(383, '其它管理', 331, 'tuisong', 'index', '', '', 0, 255, 1),
(384, '百度一键推送', 383, 'tuisong', 'index', '', '', 0, 255, 1),
(385, '拼多多商品管理', 51, 'pdditems', 'index', '', '', 0, 255, 1),
(389, '采集列表', 388, 'robots', 'index', '', '', 0, 255, 1),
(388, '采集管理', 50, 'collect', 'index', '', '', 0, 255, 1),
(390, '后台首页', 0, 'index', 'index', '', '', 0, 255, 0),
(391, '退出登录', 390, 'index', 'logout', '', '', 0, 255, 0),
(392, '中间页生成', 383, 'uland', 'index', '', '', 0, 255, 1);
ALTER TABLE `tqk_items` CHANGE `num_iid` `num_iid` BIGINT(15) NOT NULL, CHANGE `tags` `tags` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `nick` `nick` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `change_price` `change_price` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `mobilezk` `mobilezk` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `area` `area` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `fail_reason` `fail_reason` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '未通过理由', CHANGE `item_url` `item_url` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '宝贝地址', CHANGE `ems` `ems` INT(2) NULL DEFAULT '0', CHANGE `qq` `qq` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `quankouling` `quankouling` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `quanshorturl` `quanshorturl` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0', CHANGE `desc` `desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `tqk_pdditems` CHANGE `goods_id` `goods_id` BIGINT(15) NOT NULL;
ALTER TABLE `tqk_jditems` CHANGE `itemid` `itemid` BIGINT(15) NULL DEFAULT NULL COMMENT '商品ID';
DROP INDEX idx_ems ON tqk_items;
ALTER  TABLE  `tqk_link`  ADD  INDEX idx_ordid (`ordid`);
ALTER TABLE `tqk_pddorder`  ADD `fuid` INT(12) NULL,  ADD `guid` INT(12) NULL,  ADD `leve1` INT(3) NULL,  ADD `leve2` INT(3) NULL,  ADD `leve3` INT(3) NULL,  ADD `goods_name` VARCHAR(150) NULL,  ADD`status` TINYINT(1) NULL,  ADD `settle` TINYINT(1) NULL DEFAULT '0';
ALTER  TABLE  `tqk_pddorder`  ADD  INDEX idx_id (`id`);
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('basklist', '0', '');
ALTER TABLE `tqk_pdditems`  ADD `search_id` VARCHAR(60) NULL;
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('taolijin', '0', '');
ALTER TABLE `tqk_itemscate` CHANGE `pid` `pid` VARCHAR(10) NULL DEFAULT '0';
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('taolijin_pid', '', '');
ALTER TABLE `tqk_usercash`  ADD `amount` FLOAT(10,2) NULL DEFAULT '0';
INSERT INTO  `tqk_setting` (`name`, `data`, `remark`) VALUES ('jdauthkey', '', '');
ALTER TABLE `tqk_user`  ADD `jd_pid` bigint(18) NULL;
CREATE TABLE IF NOT EXISTS `tqk_jdpositionid` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `unionid` varchar(25) DEFAULT NULL COMMENT '联盟id',
  `name` varchar(30) DEFAULT NULL COMMENT '推广位名称',
  `positionid` varchar(25) DEFAULT NULL COMMENT '推广位id',
  `type` tinyint(2) DEFAULT NULL COMMENT '联盟类型',
  `uid` int(10) DEFAULT NULL COMMENT '用户UID',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `idx_id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `tqk_jdorder` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `addTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `oid` bigint(20) DEFAULT NULL COMMENT '唯一标识',
  `orderId` bigint(18) DEFAULT NULL COMMENT '订单ID',
  `orderTime` int(10) DEFAULT NULL COMMENT '下单时间',
  `finishTime` int(10) DEFAULT NULL COMMENT '完成时间',
  `modifyTime` int(10) DEFAULT NULL COMMENT '更新时间',
  `skuId` bigint(18) DEFAULT NULL COMMENT '商品ID',
  `skuName` varchar(220) DEFAULT NULL COMMENT '商品名',
  `estimateCosPrice` float(10,2) DEFAULT NULL COMMENT '预付款金额',
  `estimateFee` float(10,2) DEFAULT NULL COMMENT '预估佣金',
  `actualCosPrice` float(10,2) DEFAULT NULL COMMENT '实际计算佣金',
  `actualFee` float(10,2) DEFAULT NULL COMMENT '实际得佣金',
  `positionId` bigint(18) DEFAULT NULL COMMENT '推广位ID',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `fuid` int(10) DEFAULT NULL,
  `guid` int(10) DEFAULT NULL,
  `leve1` int(3) DEFAULT NULL,
  `leve2` int(3) DEFAULT NULL,
  `leve3` int(3) DEFAULT NULL,
  `settle` tinyint(1) DEFAULT '0',
  `validCode` int(5) DEFAULT NULL COMMENT '订单状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_oid` (`oid`) USING BTREE,
  KEY `idx_id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
ALTER TABLE `tqk_ad`  ADD `type` TINYINT(1) NULL DEFAULT '0' COMMENT '链接类型',  ADD `beginTime` INT(10) NULL COMMENT '开始时间',  ADD `endTime` INT(10) NULL COMMENT '结束时间';
ALTER TABLE `tqk_jdorder`  ADD `payMonth` INT(10) NULL DEFAULT NULL;
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('wechat_appid', '', ''), ('wechat_secret', '', ''), ('gaico', '', ''), ('gaicp', '', ''), ('elmappid', '', '');
ALTER TABLE `tqk_user`  ADD `wxAppOpenid` VARCHAR(80) NULL;
ALTER TABLE `tqk_user`  ADD `bdAppOpenid` VARCHAR(80) NULL;
ALTER TABLE `tqk_help`  ADD `url` VARCHAR(100) NULL;
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('pddappkey', '', ''), ('pddsecretkey', '', '');
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('maincolor', '#ff472b', ''), ('maintextcolor', '#ffffff', ''), ('menuleftcolor', '#ff7438 ', ''), ('menurightcolor', '#ff472b', ''), ('menuselectcolor', '#ffffff', ''), ('textcolor', '#ffffff', ''), ('textselectcolor', '#ff472b', '');
INSERT INTO `tqk_ad` (`id`, `name`, `url`, `img`, `add_time`, `ordid`, `status`, `type`, `beginTime`, `endTime`) VALUES
(1, '三只松鼠', '#', 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN015qWf9d2MgYgn3ErJj_!!3175549857.jpg', 0, 255, 8, 1, 1604966400, 1668038400),
(2, '百雀羚', '#', 'https://img.alicdn.com/imgextra/i2/3175549857/O1CN018vaUBl2MgYgkxRvGU_!!3175549857.jpg', 0, 255, 8, 1, 1604966400, 1668038400),
(3, 'T恤', '#', 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01WtrUfE2MgYgqpxKPc_!!3175549857.jpg', 0, 255, 8, 1, 1604966400, 1668038400);
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('openmt', '1', ''), ('mtappkey', '', ''), ('mtsecret', '', '');
CREATE TABLE IF NOT EXISTS `tqk_mtorder` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `smstitle` varchar(100) DEFAULT NULL,
  `orderid` varchar(50) DEFAULT NULL,
  `paytime` int(10) DEFAULT NULL,
  `payprice` float(10,2) DEFAULT NULL,
  `profit` float(10,2) DEFAULT NULL,
  `sid` varchar(30) DEFAULT NULL,
  `status` int(2) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `fuid` int(10) DEFAULT NULL,
  `guid` int(10) DEFAULT NULL,
  `leve1` int(3) DEFAULT NULL,
  `leve2` int(3) DEFAULT NULL,
  `leve3` int(3) DEFAULT NULL,
  `settle` tinyint(1) DEFAULT '0',
  `settle_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_orderid` (`orderid`) USING BTREE,
  KEY `idx_id` (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='美团订单' AUTO_INCREMENT=1 ;
UPDATE `tqk_setting` SET `data` = 'jpg,gif,png,jpeg,xls,pem' WHERE `tqk_setting`.`name` = 'attr_allow_exts';
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('payment', '1', ''), ('mch_appid', '', ''), ('mchid', '', ''), ('apikey', '', ''), ('cert_pem', '', ''), ('key_pem', '', '');
ALTER TABLE `tqk_user`  ADD `opid` VARCHAR(80) NULL;
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('wx_version', '', ''), ('bd_version', '', ''), ('bd_appid', '', ''), ('bd_secret', '', '');
INSERT INTO `tqk_menu` (`id`, `name`, `pid`, `module_name`, `action_name`, `data`, `remark`, `often`, `ordid`, `display`) VALUES (393, 'SEO关键词', 349, 'topic', 'index', '', '', 0, 255, 1);
ALTER TABLE `tqk_article`  ADD `url` VARCHAR(120) NULL;
ALTER TABLE `tqk_article`  ADD `urlid` VARCHAR(30) NULL;
update `tqk_article` set `urlid`=`id`,`url`= CONCAT('/article/view_',`id`);
DROP TABLE IF EXISTS `tqk_topic`;
CREATE TABLE `tqk_topic` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `img` varchar(120) DEFAULT NULL,
  `info` text,
  `url` varchar(150) DEFAULT NULL,
  `urlid` varchar(20) DEFAULT NULL,
  `hits` int(10) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  `add_time` varchar(13) DEFAULT NULL,
  `is_xz` tinyint(1) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `seo_title` varchar(200) DEFAULT NULL,
  `seo_key` varchar(50) DEFAULT NULL,
  `seo_des` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE IF NOT EXISTS `tqk_pid` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `pid` varchar(50) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `update_time` int(15) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `update_time` (`update_time`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='淘宝pid表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tqk_records` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `itemid` varchar(15) DEFAULT NULL,
  `ad_id` varchar(15) DEFAULT NULL,
  `uid` int(12) DEFAULT NULL,
  `create_time` int(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id` (`id`) USING BTREE,
  KEY `idx_itemid` (`itemid`) USING BTREE,
  KEY `idx_ad_id` (`ad_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('wxappid', '', ''), ('wxappsecret', '', ''), ('wxtoken', '', ''), ('wxaeskey', '', ''), ('notice', '0', ''),('tempid_1', '', ''), ('tempid_1_uid', '', ''),('tempid_2', '', ''), ('tempid_3', '', ''),('tempid_4', '', ''), ('tempid_4_time', '', '');
ALTER TABLE `tqk_robots` CHANGE `tb_cid` `tb_cid` VARCHAR(200) NULL DEFAULT NULL COMMENT '淘宝网分类ID';
update `tqk_robots` set `tb_cid`=`keyword`, `recid`=`cate_id`;
update `tqk_robots` set `keyword`='';
INSERT INTO `tqk_menu` (`id`, `name`, `pid`, `module_name`, `action_name`, `data`, `remark`, `often`, `ordid`, `display`) VALUES (394, '淘宝PID管理', 2, 'pid', 'index', '', '', 0, 255, 1);
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('unionid', '0', ''),('scan', '0', ''),('tempid_5', '', '');
ALTER TABLE `tqk_user`  ADD `unionid` VARCHAR(80) NULL DEFAULT NULL;
DELETE FROM `tqk_items` WHERE `cate_id`=11111;
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES
('dmsecret', '', ''),
('dmappkey', '', ''),
('dm_cid_dd', '', ''),
('dm_cid_mt', '', ''),
('dm_cid_kfc', '', ''),
('dm_cid_qz', '', ''),
('dm_pid', '', ''),
('mtsign', '', ''),
('dm_cid_dy', '', ''),
('payment_alipay', '1', ''),
('payment_wechat', '0', '');
CREATE TABLE IF NOT EXISTS `tqk_dmorder` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ads_id` varchar(20) DEFAULT NULL COMMENT '计划id',
  `goods_id` varchar(50) DEFAULT NULL COMMENT '商品编号',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '商品名称',
  `orders_price` decimal(10,2) DEFAULT NULL COMMENT '订单金额',
  `goods_img` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `order_status` varchar(30) DEFAULT NULL COMMENT '订单状态',
  `order_commission` decimal(10,2) DEFAULT NULL COMMENT '预估佣金',
  `order_sn` varchar(50) DEFAULT NULL COMMENT '订单号',
  `order_time` int(10) DEFAULT NULL COMMENT '下单时间',
  `uid` varchar(10) DEFAULT NULL COMMENT '反馈标签',
  `status` varchar(3) DEFAULT NULL COMMENT '状态code',
  `charge_time` int(10) DEFAULT NULL COMMENT '结算时间',
  `ads_name` varchar(60) DEFAULT NULL COMMENT '推广计划',
  `fuid` int(10) DEFAULT NULL,
  `guid` int(10) DEFAULT NULL,
  `leve1` int(3) DEFAULT NULL,
  `leve2` int(3) DEFAULT NULL,
  `leve3` int(3) DEFAULT NULL,
  `settle` tinyint(1) DEFAULT '0',
  `settle_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_order_sn` (`order_sn`) USING BTREE,
  KEY `idx_id` (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
UPDATE `tqk_setting` SET `data` = '0' WHERE `tqk_setting`.`name` = 'bingtaobao';
INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES ('apitype', '2', '微信转账接口类型');
ALTER TABLE `tqk_items` CHANGE `num_iid` `num_iid` VARCHAR(50) NOT NULL;
ALTER TABLE `tqk_items`  ADD `item_id` VARCHAR(40) NULL COMMENT 'member商品ID';
ALTER TABLE `tqk_items`  ADD UNIQUE KEY `idx_item_id` (`item_id`);
UPDATE tqk_items SET item_id = num_iid;
ALTER TABLE `tqk_records` CHANGE `itemid` `itemid` VARCHAR(39) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `tqk_order` CHANGE `goods_iid` `goods_iid` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `tqk_order`  ADD `item_id` VARCHAR(40) NULL COMMENT 'member商品ID';