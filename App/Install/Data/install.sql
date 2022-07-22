-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2018-03-14 16:44:04
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
-- 表的结构 `tqk_ad`
--
DROP TABLE IF EXISTS `tqk_ad`;
CREATE TABLE `tqk_ad` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `url` text NOT NULL,
  `img` text NOT NULL,
  `type` TINYINT(1) NULL DEFAULT '0' COMMENT '链接类型', 
  `beginTime` INT(10) NULL COMMENT '开始时间', 
  `endTime` INT(10) NULL COMMENT '结束时间', 
  `add_time` int(10) NOT NULL DEFAULT '0',
  `ordid` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_ad`
--

INSERT INTO `tqk_ad` (`id`, `name`, `url`, `img`, `add_time`, `ordid`, `status`, `type`, `beginTime`, `endTime`) VALUES
(1, '三只松鼠', '#', 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN015qWf9d2MgYgn3ErJj_!!3175549857.jpg', 0, 255, 8, 1, 1604966400, 1668038400),
(2, '百雀羚', '#', 'https://img.alicdn.com/imgextra/i2/3175549857/O1CN018vaUBl2MgYgkxRvGU_!!3175549857.jpg', 0, 255, 8, 1, 1604966400, 1668038400),
(3, 'T恤', '#', 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01WtrUfE2MgYgqpxKPc_!!3175549857.jpg', 0, 255, 8, 1, 1604966400, 1668038400),
(39, '手机', 'http://www.tuiquanke.com/article_view/1987', '/data/upload/ad/5971438c5f4f5.png', 0, 255, 1, 1, 1604966400, 1668038400),
('40', '饿了么广告', '/index.php?m=m&amp;c=elm&amp;a=index', '/data/upload/ad/5ece87d549441.png', '0', '255', '7', 1, 1604966400, 1668038400),
(41, 'banner', 'http://www.tuiquanke.com', '/data/upload/ad/5971438c5f4f5.png', 0, 255, 0, 1, 1604966400, 1668038400);

-- --------------------------------------------------------

--
-- 表的结构 `tqk_admin`
--
DROP TABLE IF EXISTS `tqk_admin`;
CREATE TABLE IF NOT EXISTS `tqk_admin` (
  `id` mediumint(6) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `role_id` smallint(5) NOT NULL,
  `last_ip` varchar(15) DEFAULT NULL,
  `last_time` int(10) DEFAULT '0',
  `email` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_adminauth`
--
DROP TABLE IF EXISTS `tqk_adminauth`;
CREATE TABLE IF NOT EXISTS `tqk_adminauth` (
  `role_id` tinyint(3) NOT NULL,
  `menu_id` smallint(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_adminauth`
--

INSERT INTO `tqk_adminauth` (`role_id`, `menu_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 17),
(1, 18),
(1, 19),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 36),
(1, 37),
(1, 38),
(1, 50),
(1, 51),
(1, 52),
(1, 54),
(1, 56),
(1, 57),
(1, 58),
(1, 59),
(1, 60),
(1, 61),
(1, 62),
(1, 63),
(1, 64),
(1, 65),
(1, 66),
(1, 70),
(1, 117),
(1, 118),
(1, 119),
(1, 148),
(1, 149),
(1, 150),
(1, 151),
(1, 152),
(1, 153),
(1, 154),
(1, 155),
(1, 156),
(1, 157),
(1, 158),
(1, 159),
(1, 160),
(1, 161),
(1, 162),
(1, 164),
(1, 181),
(1, 185),
(1, 186),
(1, 187),
(1, 190),
(1, 195),
(1, 199),
(1, 200),
(1, 201),
(1, 202),
(1, 203),
(1, 216),
(1, 232),
(1, 233),
(1, 234),
(1, 235),
(1, 236),
(1, 237),
(1, 238),
(1, 240),
(1, 242),
(1, 245),
(1, 249),
(1, 250),
(1, 256),
(1, 257),
(1, 258),
(1, 259),
(1, 260),
(1, 261),
(1, 262),
(1, 263),
(1, 264),
(1, 265),
(1, 269),
(1, 270),
(1, 271),
(1, 274),
(1, 275),
(1, 276),
(1, 277),
(1, 278),
(1, 279),
(1, 280),
(1, 281),
(1, 282),
(1, 283),
(1, 284),
(1, 286),
(1, 287),
(1, 288),
(1, 289),
(1, 290),
(1, 291),
(2, 1),
(2, 2),
(2, 3),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 12),
(2, 15),
(2, 17),
(2, 18),
(2, 19),
(2, 23),
(2, 24),
(2, 25),
(2, 34),
(2, 60),
(2, 61),
(2, 62),
(2, 63),
(2, 64),
(2, 65),
(2, 66),
(2, 148),
(2, 151),
(2, 152),
(2, 275),
(2, 282),
(2, 283),
(2, 284),
(2, 295),
(2, 338);

-- --------------------------------------------------------
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
  `order_verify_time` varchar(16) DEFAULT NULL,
  `order_settle_time` varchar(16) DEFAULT NULL,
  `uid` int(15) DEFAULT NULL,
  `goods_id` varchar(20) DEFAULT NULL,
  `fuid` INT(12) NULL,
  `guid` INT(12) NULL,
  `leve1` INT(3) NULL,
  `leve2` INT(3) NULL,
  `leve3` INT(3) NULL,
  `goods_name` VARCHAR(150) NULL,
  `status` TINYINT(1) NULL,
  `settle` TINYINT(1) NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='拼多多订单表' AUTO_INCREMENT=22 ;
--
-- 表的结构 `tqk_adminrole`
--
DROP TABLE IF EXISTS `tqk_adminrole`;
CREATE TABLE IF NOT EXISTS `tqk_adminrole` (
  `id` tinyint(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `remark` text NOT NULL,
  `ordid` tinyint(3) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_adminrole`
--

INSERT INTO `tqk_adminrole` (`id`, `name`, `remark`, `ordid`, `status`) VALUES
(1, '超级管理员', '超级管理员', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `tqk_apply`
--
DROP TABLE IF EXISTS `tqk_apply`;
CREATE TABLE IF NOT EXISTS `tqk_apply` (
  `id` int(5) NOT NULL,
  `uid` int(10) DEFAULT NULL,
  `alipay` varchar(80) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `qq` varchar(12) DEFAULT NULL,
  `add_time` varchar(15) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_article`
--
DROP TABLE IF EXISTS `tqk_article`;
CREATE TABLE `tqk_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` smallint(4) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `info` text NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览数',
  `ordid` mediumint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序值',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL,
  `url` VARCHAR(120) NULL,
  `urlid` VARCHAR(30) NULL,
  `tuisong` int(1) NOT NULL DEFAULT '0',
  `pic` varchar(255) NOT NULL DEFAULT '/static/images/nopic.jpg',
  `is_xz` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `cate_id` (`cate_id`),
  KEY `inx_ordid` (`ordid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

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

--
-- 表的结构 `tqk_articlecate`
--
DROP TABLE IF EXISTS `tqk_articlecate`;
CREATE TABLE IF NOT EXISTS `tqk_articlecate` (
  `id` smallint(4) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `pid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `spid` varchar(50) NOT NULL,
  `ordid` smallint(4) unsigned NOT NULL DEFAULT '255',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_articlecate`
--

INSERT INTO `tqk_articlecate` (`id`, `name`, `pid`, `spid`, `ordid`, `status`, `seo_title`, `seo_keys`, `seo_desc`) VALUES
(1, '优惠券头条', 0, '0', 255, 1, '淘宝优惠券头条', '淘宝优惠券头条', '淘宝优惠券头条'),
(2, '公告通知', 0, '0', 255, 1, '', '', ''),
(3, '居家生活', 0, '0', 255, 1, '', '', ''),
(4, '发现好物', 0, '0', 255, 1, '', '', ''),
(5, '电子数码', 0, '0', 255, 1, '', '', ''),
(6, '时尚运动', 0, '0', 255, 1, '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `tqk_dmorder`
--

CREATE TABLE IF NOT EXISTS `tqk_dmorder` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ads_id` varchar(20) DEFAULT NULL COMMENT '计划id',
  `goods_id` varchar(50) DEFAULT NULL COMMENT '商品编号',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '商品名称',
  `orders_price` decimal(10,2) DEFAULT NULL COMMENT '订单金额',
  `goods_img` varchar(200) DEFAULT NULL COMMENT '商品图片',
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

--
-- 表的结构 `tqk_balance`
--
DROP TABLE IF EXISTS `tqk_balance`;
CREATE TABLE IF NOT EXISTS `tqk_balance` (
  `id` int(11) NOT NULL,
  `uid` int(10) DEFAULT NULL,
  `money` float(10,2) DEFAULT NULL,
  `name` varchar(10) DEFAULT NULL,
  `method` tinyint(1) DEFAULT NULL COMMENT '1为微信/2为支付宝',
  `allpay` varchar(30) DEFAULT NULL,
  `content` text,
  `status` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_basklist`
--
DROP TABLE IF EXISTS `tqk_basklist`;
CREATE TABLE IF NOT EXISTS `tqk_basklist` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `orderid` int(11) DEFAULT NULL COMMENT '//订单id',
  `order_sn` varchar(50) DEFAULT NULL COMMENT '//订单号',
  `content` varchar(255) DEFAULT NULL,
  `img` varchar(100) DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL,
  `create_time` varchar(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `integray` mediumint(5) DEFAULT NULL COMMENT '//积分',
  `type` tinyint(1) DEFAULT '0' COMMENT '//1为精华帖，2为普通帖'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_basklistlogo`
--
DROP TABLE IF EXISTS `tqk_basklistlogo`;
CREATE TABLE IF NOT EXISTS `tqk_basklistlogo` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `order_sn` varchar(50) DEFAULT NULL,
  `integray` mediumint(5) DEFAULT NULL COMMENT '//积分',
  `remark` varchar(50) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

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
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_brand`
--

INSERT INTO `tqk_brand` (`id`, `cate_id`, `logo`, `brand`, `recommend`, `status`, `ordid`, `add_time`, `remark`) VALUES
(7, 14, '/data/upload/avatar/5a2df3e952156.png', '九阳', 1, 1, 255, '', '最高可省200元'),
(8, 17, '/data/upload/avatar/5a2df5618446a.png', '波司登', 1, 1, 255, '', ''),
(9, 11, '/data/upload/avatar/5a2e14cdbbb50.jpg', '百雀羚', 1, 1, 255, '', ''),
(10, 11, '/data/upload/avatar/5a2e14dc6892c.jpg', '佰草集', 1, 1, 255, '', ''),
(11, 11, '/data/upload/avatar/5a2e14f00e03c.jpg', '菲诗小铺', 1, 1, 255, '', ''),
(12, 13, '/data/upload/avatar/5a2fe38dba8f5.jpg', '苏泊尔', 1, 1, 255, '', ''),
(13, 17, '/data/upload/avatar/5a2fe543d3481.jpg', '恒源祥', 1, 1, 255, '', ''),
(14, 11, '/data/upload/avatar/5a2fe69461f7b.jpg', '自然堂', 1, 1, 255, '', ''),
(15, 9, '/data/upload/avatar/5a3128ad23039.jpg', '百草味', 1, 1, 255, '', ''),
(16, 9, '/data/upload/avatar/5a3128d2db7cb.jpg', '良品铺子', 1, 1, 255, '', ''),
(17, 9, '/data/upload/avatar/5a3128e4270b1.jpg', '徐福记', 1, 1, 255, '', ''),
(18, 9, '/data/upload/avatar/5a3129058c49d.jpg', '盼盼食品', 1, 1, 255, '', ''),
(19, 9, '/data/upload/avatar/5a31293e48ee6.jpg', '雀巢', 1, 1, 255, '', ''),
(20, 10, '/data/upload/avatar/5a31296944578.jpg', '施华洛世奇', 1, 1, 255, '', ''),
(21, 10, '/data/upload/avatar/5a31297eb3c32.jpg', '六福珠宝', 1, 1, 255, '', ''),
(22, 10, '/data/upload/avatar/5a31298fa3e41.jpg', '慈元阁', 1, 1, 255, '', ''),
(23, 11, '/data/upload/avatar/5a3129b124cea.jpg', '韩后', 1, 1, 255, '', ''),
(24, 16, '/data/upload/avatar/5a3129cb05949.jpg', '十月结晶', 1, 1, 255, '', ''),
(25, 17, '/data/upload/avatar/5a3129e761a57.jpg', '七匹狼', 1, 1, 255, '', ''),
(26, 17, '/data/upload/avatar/5a312a35aaa86.jpg', '雅鹿', 1, 1, 255, '', ''),
(27, 17, '/data/upload/avatar/5a312a45b3c5c.jpg', '红豆内衣', 1, 1, 255, '', ''),
(28, 18, '/data/upload/avatar/5a312a61012a0.jpg', '特步', 1, 1, 255, '', ''),
(29, 18, '/data/upload/avatar/5a312a735447e.jpg', '达芙妮', 1, 1, 255, '', ''),
(30, 12, '/data/upload/avatar/5a312a84d9e9b.jpg', '晨光', 1, 1, 255, '', ''),
(31, 12, '/data/upload/avatar/5a312a98e3c79.jpg', '得力', 1, 1, 255, '', ''),
(32, 15, '/data/upload/avatar/5a312aab01ea9.jpg', '稻草人', 1, 1, 255, '', ''),
(33, 15, '/data/upload/avatar/5a312ac130780.jpg', '老人头', 1, 1, 255, '', ''),
(34, 14, '/data/upload/avatar/5a312b117e0f6.jpg', '品胜', 1, 1, 255, '', ''),
(35, 14, '/data/upload/avatar/5a312b301b3e6.jpg', '罗技', 1, 1, 255, '', ''),
(36, 14, '/data/upload/avatar/5a312b551f218.jpg', '海尔', 1, 1, 255, '', ''),
(37, 14, '/data/upload/avatar/5a312b6423e16.jpg', '飞利浦', 1, 1, 255, '', ''),
(38, 18, '/data/upload/avatar/5a312b8e6e0a2.jpg', '李宁', 1, 1, 255, '', ''),
(39, 17, '/data/upload/avatar/5a312c0c17d83.jpg', '迪士尼', 1, 1, 255, '', ''),
(40, 11, '/data/upload/avatar/5a312c42d930f.jpg', '美肤宝', 1, 1, 255, '', ''),
(41, 11, '/data/upload/avatar/5a312d4c815dd.jpg', '欧莱雅', 1, 1, 255, '', ''),
(42, 11, '/data/upload/avatar/5a312d6912935.jpg', '欧诗漫', 1, 1, 255, '', '');

-- --------------------------------------------------------

--
-- 表的结构 `tqk_brandcate`
--
DROP TABLE IF EXISTS `tqk_brandcate`;
CREATE TABLE IF NOT EXISTS `tqk_brandcate` (
  `id` smallint(4) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `pid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `spid` varchar(50) NOT NULL,
  `ordid` smallint(4) unsigned NOT NULL DEFAULT '255',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_brandcate`
--

INSERT INTO `tqk_brandcate` (`id`, `name`, `pid`, `spid`, `ordid`, `status`, `seo_title`, `seo_keys`, `seo_desc`) VALUES
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

-- --------------------------------------------------------

--
-- 表的结构 `tqk_finance`
--
DROP TABLE IF EXISTS `tqk_finance`;
CREATE TABLE IF NOT EXISTS `tqk_finance` (
  `id` int(10) NOT NULL,
  `add_time` varchar(15) DEFAULT NULL,
  `price` float(10,2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `backcash` float(10,2) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `income` float(10,2) DEFAULT NULL,
  `mark` varchar(200) DEFAULT NULL,
  `type` tinyint(1) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_help`
--
DROP TABLE IF EXISTS `tqk_help`;
CREATE TABLE IF NOT EXISTS `tqk_help` (
  `id` int(11) unsigned NOT NULL,
  `title` varchar(120) NOT NULL,
  `info` text NOT NULL,
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL,
  `url` VARCHAR(100) NULL,
  `last_time` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_help`
--

INSERT INTO `tqk_help` (`id`, `title`, `info`, `seo_title`, `seo_keys`, `seo_desc`, `last_time`) VALUES
(1, '关于我们', '<p style="padding-bottom:0px;widows:2;text-transform:none;background-color:#ffffff;text-indent:0px;margin:0px;padding-left:0px;letter-spacing:normal;padding-right:0px;font:12px/30px Tahoma,Helvetica,Arial,宋体,sans-serif;word-wrap:break-word;white-space:normal;orphans:2;color:#5e5e5e;word-spacing:0px;padding-top:0px;-webkit-text-size-adjust:auto;-webkit-text-stroke-width:0px;">\n	<p style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:14px;line-height:24px;color:#333333;font-family:&quot;white-space:normal;background-color:#FFFFFF;">\n		<span style="font-family:&quot;font-size:14px;white-space:normal;background-color:#FFFFFF;">尊敬的推客用户<br />\n<br />\n</span>\n	</p>\n	<p style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:14px;line-height:24px;color:#333333;font-family:&quot;white-space:normal;background-color:#FFFFFF;">\n		<span style="font-family:&quot;font-size:14px;white-space:normal;background-color:#FFFFFF;">&nbsp; &nbsp; &nbsp; 由于推券客提供的微信公众号服务器网址使用用户过多，微信要求必须进行第三方平台认证。<br />\n</span><br />\n因此我们做了以下调整：\n	</p>\n	<p style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:14px;line-height:24px;color:#333333;font-family:&quot;white-space:normal;background-color:#FFFFFF;">\n		<br />\n1. 老的公众号服务器网址 到10月8号无法继续使用。<br />\n<br />\n2.&nbsp; 请登录推券客平台进入&nbsp; “站点设置 ” 获取新的公众号服务器网址 （注：此网址已经通过微信第三方平台认证）\n	</p>\n	<p style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:14px;line-height:24px;color:#333333;font-family:&quot;white-space:normal;background-color:#FFFFFF;">\n		<br />\n	</p>\n	<p style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:14px;line-height:24px;color:#333333;font-family:&quot;white-space:normal;background-color:#FFFFFF;">\n		3.&nbsp; 取消 公众号直播网址 （注: 推券客CMS V1.8版本已经将“优惠直播”模块迁移到手机站，不再需要此直播网址支持，\n	</p>\n	<p style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:14px;line-height:24px;color:#333333;font-family:&quot;white-space:normal;background-color:#FFFFFF;">\n		所以你的网站后台直播配置中可以不用填写）<br />\n<br />\n<span style="color:#E53333;font-size:16px;">特别提醒：因推券客CMS是开源的程序，里面的逻辑架构技术人员很容易看明白。</span>\n	</p>\n	<p style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:14px;line-height:24px;color:#333333;font-family:&quot;white-space:normal;background-color:#FFFFFF;">\n		<span style="color:#E53333;font-size:16px;">所以公众号</span><span style="color:#E53333;font-size:16px;">服务器网址和通行密钥请不要随便泄露，否则会对你的网站安全造成隐患。</span>\n	</p>\n	<p style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:14px;line-height:24px;color:#333333;font-family:&quot;white-space:normal;background-color:#FFFFFF;">\n		<span style="color:#E53333;font-size:16px;"><br />\n</span>\n	</p>\n	<p style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:14px;line-height:24px;color:#333333;font-family:&quot;white-space:normal;background-color:#FFFFFF;">\n		<span style="color:#E53333;font-size:16px;"><br />\n</span>\n	</p>\n	<p style="margin-top:0px;margin-bottom:0px;padding:0px;font-size:14px;line-height:24px;color:#333333;font-family:&quot;white-space:normal;background-color:#FFFFFF;">\n		<span style="color:#E53333;font-size:16px;"><br />\n</span>\n	</p>\n</p>', '', '', '', 1507858298),
(2, '联系我们', '<p style="margin-top:0px;margin-bottom:0px;padding:0px;color:#5E5E5E;font-family:Tahoma,Helvetica,Arial,宋体,sans-serif;line-height:30px;white-space:normal;background-color:#FFFFFF;">\n	<br />\n</p>', '', '', '', 1488612868),
(3, '免责声明', '<p style="margin-top:0px;margin-bottom:0px;padding:0px;color:#5E5E5E;font-family:Tahoma,Helvetica,Arial,宋体,sans-serif;line-height:30px;white-space:normal;background-color:#FFFFFF;">\n	<br />\n</p>', '', '', '', 1488612873),
(4, '新手指南', '', '', '', '', 1507611171);

-- --------------------------------------------------------

--
-- 表的结构 `tqk_jditems`
--
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
  `itemid` BIGINT(15) NULL DEFAULT NULL COMMENT '商品ID',
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_hongbaodetail`
--
DROP TABLE IF EXISTS `tqk_hongbaodetail`;
CREATE TABLE IF NOT EXISTS `tqk_hongbaodetail` (
  `id` int(10) NOT NULL,
  `hid` int(10) DEFAULT NULL,
  `price` varchar(5) DEFAULT NULL,
  `add_time` varchar(11) DEFAULT NULL,
  `get_time` varchar(11) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_mtorder`
--
DROP TABLE IF EXISTS `tqk_mtorder`;
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

-- --------------------------------------------------------

--
-- 表的结构 `tqk_items`
--
DROP TABLE IF EXISTS `tqk_items`;
CREATE TABLE `tqk_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordid` int(11) DEFAULT '9999' COMMENT '自定义排序',
  `cate_id` int(11) DEFAULT '0' COMMENT '属于分类',
  `ali_id` varchar(50) DEFAULT NULL,
  `zc_id` int(11) DEFAULT '0' COMMENT '专场',
  `orig_id` smallint(6) DEFAULT '1',
  `num_iid` varchar(50) NOT NULL,
  `item_id` varchar(40) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `intro` varchar(255) DEFAULT NULL,
  `tags`  varchar(50)  DEFAULT NULL,
  `nick` varchar(50) DEFAULT NULL,
  `change_price` varchar(20) DEFAULT NULL,
  `mobilezk` varchar(20) NOT NULL,
  `area` varchar(10) NOT NULL,
  `sellerId` varchar(21) DEFAULT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '1',
  `uname` varchar(20) NOT NULL DEFAULT 'admin',
  `pic_url` varchar(200) NOT NULL,
  `pic_urls` text COMMENT '宽版图片',
  `price` decimal(11,2) NOT NULL,
  `link` varchar(200) DEFAULT NULL,
  `click_url` varchar(200) NOT NULL DEFAULT '0',
  `ding` int(1) NOT NULL DEFAULT '0' COMMENT '0',
  `volume` int(11) NOT NULL,
  `commission` decimal(11,2) NOT NULL,
  `commission_rate` int(11) NOT NULL,
  `tk_commission_rate` int(11) DEFAULT NULL,
  `coupon_type` int(11) DEFAULT NULL,
  `coupon_price` decimal(11,2) NOT NULL,
  `coupon_rate` int(11) NOT NULL,
  `coupon_start_time` int(11) DEFAULT NULL,
  `coupon_end_time` int(11) DEFAULT NULL,
  `pass` int(11) DEFAULT '0' COMMENT '是否审核',
  `status` varchar(20) DEFAULT 'underway' COMMENT '出售状态',
  `fail_reason` varchar(60) DEFAULT NULL COMMENT '未通过理由',
  `shop_name` varchar(50) DEFAULT NULL,
  `shop_type` varchar(11) DEFAULT NULL,
  `item_url` varchar(55) DEFAULT NULL COMMENT '宝贝地址',
  `ems` int(2) DEFAULT '0',
  `qq` varchar(11) DEFAULT NULL,
  `mobile` varchar(13) DEFAULT '',
  `realname` varchar(25) DEFAULT NULL,
  `hits` int(11) DEFAULT '0' COMMENT '点击量',
  `isshow` int(11) DEFAULT '1',
  `likes` int(11) DEFAULT '0',
  `inventory` int(11) DEFAULT '0' COMMENT '库存',
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keys` varchar(255) DEFAULT NULL,
  `seo_desc` text,
  `add_time` int(11) DEFAULT NULL,
  `last_rate_time` int(10) NOT NULL DEFAULT '0',
  `is_collect_comments` int(1) DEFAULT '0' COMMENT '是否采集了淘宝评论1表示已经采集了淘宝评论',
  `isq` smallint(1) NOT NULL DEFAULT '0',
  `quan` int(10) NOT NULL,
  `quanurl` text,
  `quankouling` varchar(200) DEFAULT NULL,
  `quanshorturl` varchar(40) DEFAULT '0',
  `Quan_condition` varchar(5) NOT NULL,
  `Quan_surplus` int(5) NOT NULL,
  `Quan_receive` int(5) NOT NULL,
  `tk` int(1) NOT NULL DEFAULT '0' COMMENT '默认0',
  `que` int(1) NOT NULL DEFAULT '0' COMMENT '默认0',
  `quan_pl` varchar(1) DEFAULT '0',
  `quan_rq` varchar(1) DEFAULT '0',
  `last_time` int(11) DEFAULT '0',
  `Quan_id` varchar(32) NOT NULL,
  `desc` text,
  `tuisong` int(1) NOT NULL DEFAULT '0' COMMENT '是否推送',
  `is_commend` tinyint(1) DEFAULT '0',
  `up_time` varchar(20) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `num_iid` (`num_iid`),
  UNIQUE KEY `idx_item_id` (`item_id`),
  KEY `idx_id` (`id`),
  KEY `idx_ordid` (`ordid`),
  KEY `idx_is_commend` (`is_commend`),
  KEY `idx_volume` (`volume`),
  KEY `idx_coupon_price` (`coupon_price`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


--
-- 表的结构 `tqk_itemscate`
--
DROP TABLE IF EXISTS `tqk_itemscate`;
CREATE TABLE IF NOT EXISTS `tqk_itemscate` (
  `id` smallint(4) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `ali_id` varchar(50) DEFAULT NULL,
  `tags` varchar(50) NOT NULL,
  `pid` varchar(10) NULL DEFAULT '0',
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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_itemscate`
--

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

-- --------------------------------------------------------

--
-- 表的结构 `tqk_itemslike`
--
DROP TABLE IF EXISTS `tqk_itemslike`;
CREATE TABLE IF NOT EXISTS `tqk_itemslike` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_itemsorig`
--
DROP TABLE IF EXISTS `tqk_itemsorig`;
CREATE TABLE IF NOT EXISTS `tqk_itemsorig` (
  `id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ordid` tinyint(3) unsigned NOT NULL DEFAULT '255'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_itemsorig`
--

INSERT INTO `tqk_itemsorig` (`id`, `img`, `name`, `type`, `url`, `ordid`) VALUES
(1, '50b2e721a6c1e_thumb.jpg', '淘宝', 'C', 'taobao.com', 0),
(2, '50b2e726d4ade_thumb.jpg', '天猫', 'B', 'tmall.com', 0);

-- --------------------------------------------------------

--
-- 表的结构 `tqk_link`
--
DROP TABLE IF EXISTS `tqk_link`;
CREATE TABLE IF NOT EXISTS `tqk_link` (
  `id` smallint(4) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `img` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ordid` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_link`
--

INSERT INTO `tqk_link` (`id`, `name`, `img`, `url`, `ordid`, `status`) VALUES
(14, '淘宝优惠券', '', 'https://www.kemaide.com', 0, 1),
(13, '淘宝客是什么', '', 'http://www.tuiquanke.com', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `tqk_menu`
--
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
(371, '积分日志', 363, 'basklist', 'logs', '', '', 0, 255, 1),
(372, '品牌专场', 50, 'brand', 'index', '', '', 0, 255, 1),
(373, '品牌列表', 372, 'brand', 'index', '', '', 0, 255, 1),
(374, '品牌分类', 372, 'brandcate', 'index', '', '', 0, 255, 1),
(383, '其它管理', 331, 'tuisong', 'index', '', '', 0, 255, 1),
(384, '百度一键推送', 383, 'tuisong', 'index', '', '', 0, 255, 1),
(389, '采集列表', 388, 'robots', 'index', '', '', 0, 255, 1),
(388, '采集管理', 50, 'collect', 'index', '', '', 0, 255, 1),
(390, '后台首页', 0, 'index', 'index', '', '', 0, 255, 0),
(391, '退出登录', 390, 'index', 'logout', '', '', 0, 255, 0),
(392, '中间页生成', 383, 'uland', 'index', '', '', 0, 255, 1),
(393, 'SEO关键词', 349, 'topic', 'index', '', '', 0, 255, 1),
(394, '淘宝PID管理', 2, 'pid', 'index', '', '', 0, 255, 1);

-- --------------------------------------------------------

--
-- 表的结构 `tqk_nav`
--
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

--
-- 转存表中的数据 `tqk_nav`
--

INSERT INTO `tqk_nav` (`id`, `type`, `name`, `alias`, `link`, `target`, `ordid`, `mod`, `status`) VALUES
(1, '0', '超级人气榜', 'top100', '/index.php/top100', 0, 2, '', 1),
(2, '0', '京东优惠券', 'jd', '/index.php/jd', 0, 5, '', 1),
(3, '0', '品牌优惠券', 'brand', '/index.php/brand', 0, 3, '', 1),
(4, '0', '9块9包邮', 'jiu', '/index.php/jiu', 0, 2, '', 1),
(6, '0', '优惠券头条', 'article', '/index.php/article', 0, 255, '', 1),
(8, '0', '拼多多券', 'pdd', '/index.php/pdd', 0, 6, '', 1),
(12, '1', '淘宝优惠券', 'cate', '/index.php/cate', 1, 4, 'alias', 1);

-- --------------------------------------------------------

--
-- 表的结构 `tqk_order`
--
DROP TABLE IF EXISTS `tqk_order`;
CREATE TABLE IF NOT EXISTS `tqk_order` (
  `id` int(10) NOT NULL,
  `orderid` varchar(50) DEFAULT NULL,
  `uid` int(10) DEFAULT '0',
  `add_time` varchar(30) DEFAULT NULL COMMENT '下单时间',
  `status` tinyint(1) DEFAULT '0',
  `price` float(10,2) DEFAULT NULL COMMENT '付款金额',
  `integral` int(10) DEFAULT NULL COMMENT '获得积分',
  `up_time` varchar(30) DEFAULT NULL COMMENT '结算时间',
  `goods_iid` varchar(50) DEFAULT NULL COMMENT '商品id',
  `item_id` varchar(40) DEFAULT NULL COMMENT 'member商品id',
  `goods_title` varchar(160) DEFAULT NULL COMMENT '商品名称',
  `goods_num` int(3) DEFAULT NULL COMMENT '商品数量',
  `ad_id` varchar(50) DEFAULT NULL COMMENT '推广位id',
  `ad_name` varchar(80) DEFAULT NULL COMMENT '推广位名称',
  `goods_rate` varchar(10) DEFAULT NULL COMMENT '佣金比例',
  `income` float(10,2) DEFAULT '0.00' COMMENT '效果预估',
  `fuid` INT( 12 ) NULL,
  `guid` INT( 12 ) NULL,
  `leve1` INT( 3 ) NULL DEFAULT  '0',
  `leve2` INT( 3 ) NULL DEFAULT  '0',
  `leve3` INT( 3 ) NULL DEFAULT  '0',
  `nstatus` TINYINT( 1 ) NULL DEFAULT  '0',
  `relation_id` varchar(30) DEFAULT NULL,
  `special_id` varchar(30) DEFAULT NULL,
  `bask` TINYINT(1) NULL DEFAULT '0',
  `settle` TINYINT(1) NULL DEFAULT '0',
  `click_time` varchar(13) DEFAULT NULL COMMENT '点击时间',
  `oid` varchar(60) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_robots`
--
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

-- --------------------------------------------------------

--
-- 表的结构 `tqk_setting`
--
DROP TABLE IF EXISTS `tqk_setting`;
CREATE TABLE IF NOT EXISTS `tqk_setting` (
  `name` varchar(100) NOT NULL,
  `data` text NOT NULL,
  `remark` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tqk_setting`
--

INSERT INTO `tqk_setting` (`name`, `data`, `remark`) VALUES
('site_name', '推券客联盟', ''),
('site_url', 'http://192.168.1.168', ''),
('headerm_html', 'http://192.168.1.168/index.php/m', ''),
('site_icp', '苏ICP备9999999号', ''),
('qq', '234234234', ''),
('robots_key', '', ''),
('app_kehuduan', '', ''),
('dingdan', '￥|￥', ''),
('statistics_code', '', ''),
('taojindian_html', '', ''),
('jingpintie', '10', ''),
('putongtie', '15', ''),
('ipban_switch', '0', ''),
('site_status', '1', ''),
('closed_reason', '网站升级中。。', ''),
('index_page_size', '40', ''),
('index_not_text', '', ''),
('index_cids', 'a:11:{i:0;s:1:"2";i:1;s:1:"3";i:2;s:1:"6";i:3;s:1:"7";i:4;s:1:"8";i:5;s:1:"9";i:6;s:2:"10";i:7;s:2:"18";i:8;s:2:"17";i:9;s:2:"16";i:10;s:2:"12";}', ''),
('index_sort', 'id  desc', ''),
('index_shop_type', '', ''),
('index_mix_price', '0', ''),
('index_max_price', '9999999', ''),
('index_mix_volume', '0', ''),
('index_max_volume', '9999999', ''),
('wait_time', '0', ''),
('end_time', '0', ''),
('index_ems', '0', ''),
('dn_item_desc', '1', ''),
('sj_item_desc', '0', ''),
('seo_config', 'a:8:{s:5:"index";a:3:{s:5:"title";s:21:"推券客CMS演示站";s:8:"keywords";s:11:"关键字45";s:11:"description";s:8:"描述54";}s:4:"cate";a:3:{s:5:"title";s:45:" {cate_name}最新优惠券大全_{site_name}";s:8:"keywords";s:14:"{seo_keywords}";s:11:"description";s:17:"{seo_description}";}s:4:"item";a:3:{s:5:"title";s:7:"{title}";s:8:"keywords";s:14:"{seo_keywords}";s:11:"description";s:17:"{seo_description}";}s:6:"xinpin";a:3:{s:5:"title";s:12:"{site_title}";s:8:"keywords";s:2:"ui";s:11:"description";s:2:"43";}s:3:"jiu";a:3:{s:5:"title";s:16:"9块9{site_name}";s:8:"keywords";s:0:"";s:11:"description";s:0:"";}s:5:"ershi";a:3:{s:5:"title";s:12:"{site_title}";s:8:"keywords";s:1:"0";s:11:"description";s:0:"";}s:8:"jingxuan";a:3:{s:5:"title";s:12:"{site_title}";s:8:"keywords";s:2:"te";s:11:"description";s:0:"";}s:6:"top100";a:3:{s:5:"title";s:12:"{site_title}";s:8:"keywords";s:0:"";s:11:"description";s:0:"";}}', ''),
('zhibo_model', '0', ''),
('person_num', '1000', ''),
('zhibo_url', '', ''),
('zhibo_shop_type', '0', ''),
('zhibo_mix_price', '0', ''),
('zhibo_max_price', '0', ''),
('taobao_pid', '', ''),
('site_cache', '0', ''),
('site_cachepath', '', ''),
('site_cachetime', '3600', ''),
('item_hit', '0', ''),
('coupon_add_time', '840', ''),
('index_admin', 'admin', ''),
('youhun_secret', '', ''),
('site_secret', '0', ''),
('site_tiaozhuan', '1', ''),
('attach_path', 'data/upload/', ''),
('attr_allow_exts', 'jpg,gif,png,jpeg,xls,pem,csv', ''),
('attr_allow_size', '2000', ''),
('site_logo', '/data/upload/site/597142e8bbcc7.png', ''),
('site_flogo', '/data/upload/site/59dc17e6d999f.png', ''),
('site_background', '/data/upload/site/59b79fa615434.png', ''),
('gongju', '', ''),
('kouling', '1', ''),
('taobao_appkey', '', ''),
('taobao_appsecret', '', ''),
('app_key', '欢迎加入推券客联盟', ''),
('site_zhibo', '/data/upload/site/59e8020f75f2c.png', ''),
('fanxian', '30', ''),
('zhibo_mix_volume', '100', ''),
('bili1', '', ''),
('bili2', '', ''),
('bili3', '', ''),
('agentcondition',  '300',  ''),
('islogin',  '0',  ''),
('Quota', '', ''),
('ComputingTime', '', ''),
('invocode', '1', ''),
('qrcode', '0', ''),
('reinte', '0', ''),
('isfanli', '1', ''),
('openduoduo', '1', ''),
('sms_status', '0', ''),
('sms_appid', '', ''),
('sms_appkey', '', ''),
('sms_sign', '', ''),
('sms_reg_id', '', ''),
('sms_fwd_id', '', ''),
('sms_my_phone', '', ''),
('sms_tixian_id', '', ''),
('api_line', 'http://api.tuiquanke.cn', ''),
('bingtaobao', '0', ''),
('invitecode', '', ''),
('jdappkey', '', ''), 
('jdsecretkey', '', ''), 
('jdpid', '', ''), 
('openjd', '', ''), 
('basklist', '0', ''), 
('taolijin', '0', ''), 
('taolijin_pid', '', ''), 
('jdauthkey', '', ''),
('wechat_appid', '', ''),
('wechat_secret', '', ''),
('gaico', '', ''),
('gaicp', '', ''),
('elmappid', '', ''),
('pddappkey', '', ''),
('pddsecretkey', '', ''),
('maincolor', '#fc4955', ''),
('maintextcolor', '#ffffff', ''),
('menuleftcolor', '#fc4955 ', ''), 
('menurightcolor', '#771D32', ''),
('menuselectcolor', '#ffffff', ''),
('textcolor', '#ffffff', ''),
('textselectcolor', '#fc4955', ''), 
('openmt', '1', ''),
('mtappkey', '', ''),
('mtsecret', '', ''), 
('payment', '1', ''), 
('mch_appid', '', ''), 
('mchid', '', ''), 
('apikey', '', ''), 
('cert_pem', '', ''), 
('key_pem', '', ''),
('wx_version', '', ''), 
('bd_version', '', ''), 
('bd_appid', '', ''), 
('bd_secret', '', ''),
('wxappid', '', ''),
('wxappsecret', '', ''),
('wxtoken', '', ''),
('wxaeskey', '', ''),
('notice', '0', ''),
('tempid_1', '', ''),
('tempid_1_uid', '',''),
('tempid_4_time','',''),
('tempid_4', '', ''),
('tempid_2', '', ''),
('tempid_3', '', ''),
('tempid_5', '', ''),
('unionid', '0', ''),
('scan', '0', ''),
('wm_settle', '0', ''),
('dmappkey', '', ''),
('dmsecret', '', ''),
('dm_cid_dd', '', ''),
('dm_cid_mt', '', ''),
('dm_cid_kfc', '', ''),
('dm_cid_qz', '', ''),
('dm_cid_dy', '', ''),
('dm_pid', '', ''),
('mtsign', '', ''),
('payment_alipay', '1', ''),
('payment_wechat', '0', ''),
('apitype', '2', ''),
('zhunru', '', '');
-- --------------------------------------------------------

--
-- 表的结构 `tqk_user`
--
DROP TABLE IF EXISTS `tqk_user`;
CREATE TABLE IF NOT EXISTS `tqk_user` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL DEFAULT '0',
  `nickname` varchar(50) DEFAULT NULL,
  `password` varchar(32) NOT NULL DEFAULT '0',
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `avatar` varchar(250) DEFAULT NULL,
  `money` float(10,2) DEFAULT '0.00',
  `freeze_fee` float(10,2) DEFAULT '0.00' COMMENT '//冻结资金',
  `score` int(10) unsigned DEFAULT '0',
  `wechat` varchar(20) DEFAULT NULL,
  `qq` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `reg_ip` varchar(15) NOT NULL,
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_ip` varchar(15) DEFAULT '0',
  `login_count` int(10) DEFAULT '1',
  `create_time` int(10) DEFAULT '0',
  `state` tinyint(1) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `openid` varchar(80) DEFAULT '0',
  `frozen` float(10,2) DEFAULT '0.00',
  `webmaster` tinyint(1) DEFAULT '0',
  `webmaster_pid` varchar(40) DEFAULT NULL,
  `webmaster_rate` int(5) DEFAULT NULL,
  `pdd_pid` varchar(30) DEFAULT NULL,
  `oid` varchar(60) DEFAULT NULL,
  `fuid` INT( 12 ) NULL,
  `guid` INT( 12 ) NULL,
  `invocode` VARCHAR( 30 ) NULL,
  `realname` VARCHAR( 30 ) NULL,
  `alipay` VARCHAR( 40 ) NULL,
  `relation_id` VARCHAR(30) NULL,
  `special_id` VARCHAR(30) NULL,
  `tb_refresh_token` VARCHAR(255) NULL,
  `expire_time` VARCHAR(30) NULL,
  `tb_open_uid` VARCHAR(100) NULL,
  `tb_ access_token` VARCHAR(255) NULL,
  `wxAppOpenid` VARCHAR(80) NULL,
  `bdAppOpenid` VARCHAR(80) NULL,
   `jd_pid` bigint(18) NULL,
   `opid` VARCHAR(80) NULL,
	`unionid` varchar(80) DEFAULT NULL,
  `tbname` varchar(50) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_usercash`
--
DROP TABLE IF EXISTS `tqk_usercash`;
CREATE TABLE IF NOT EXISTS `tqk_usercash` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `money` float(10,2) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_jdpositionid`
--
DROP TABLE IF EXISTS `tqk_jdpositionid`;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_jdorder`
--
DROP TABLE IF EXISTS `tqk_jdorder`;
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
  `payMonth` int(10) DEFAULT NULL,
  `validCode` int(5) DEFAULT NULL COMMENT '订单状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_oid` (`oid`) USING BTREE,
  KEY `idx_id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tqk_pdditems`
--
DROP TABLE IF EXISTS `tqk_pdditems`;
CREATE TABLE `tqk_pdditems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` BIGINT(15) NOT NULL,
  `goods_name` varchar(255) NOT NULL,
  `goods_desc` varchar(255) DEFAULT NULL,
  `goods_thumbnail_url` varchar(120) NOT NULL,
  `goods_image_url` varchar(120) NOT NULL,
  `sold_quantity` int(11) NOT NULL,
  `min_group_price` decimal(11,2) NOT NULL,
  `min_normal_price` decimal(11,2) NOT NULL,
  `mall_name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `coupon_discount` decimal(10,0) NOT NULL,
  `coupon_total_quantity` int(11) NOT NULL,
  `coupon_remain_quantity` int(11) NOT NULL,
  `coupon_start_time` int(11) NOT NULL,
  `coupon_end_time` int(11) NOT NULL,
  `addtime` int(11) NOT NULL,
  `quanurl` varchar(200) DEFAULT NULL,
  `orderid` int(5) NOT NULL DEFAULT '999',
  `tuisong` tinyint(1) NOT NULL DEFAULT '0',
  `promotion_rate` int(10) DEFAULT NULL,
  `search_id` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_id` (`goods_id`),
  KEY `idx_id` (`id`),
  KEY `idx_orderid` (`orderid`) USING BTREE,
  KEY `idx_addtime` (`addtime`) USING BTREE,
  KEY `idx_min_group_price` (`min_group_price`) USING BTREE,
  KEY `idx_coupon_discount` (`coupon_discount`) USING BTREE,
  KEY `idx_sold_quantity` (`sold_quantity`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='拼多多商品表';

--
-- Indexes for dumped tables
--

CREATE TABLE IF NOT EXISTS `tqk_pid` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `pid` varchar(50) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `update_time` int(15) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `update_time` (`update_time`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='淘宝pid表' AUTO_INCREMENT=1 ;

--
-- 表的结构 `tqk_records`
--

CREATE TABLE IF NOT EXISTS `tqk_records` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `itemid` varchar(40) DEFAULT NULL,
  `ad_id` varchar(15) DEFAULT NULL,
  `uid` int(12) DEFAULT NULL,
  `create_time` int(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id` (`id`) USING BTREE,
  KEY `idx_itemid` (`itemid`) USING BTREE,
  KEY `idx_ad_id` (`ad_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Indexes for table `tqk_admin`
--
ALTER TABLE `tqk_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`username`);

--
-- Indexes for table `tqk_adminauth`
--
ALTER TABLE `tqk_adminauth`
  ADD KEY `role_id` (`role_id`,`menu_id`);

--
-- Indexes for table `tqk_adminrole`
--
ALTER TABLE `tqk_adminrole`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_apply`
--
ALTER TABLE `tqk_apply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_articlecate`
--
ALTER TABLE `tqk_articlecate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_balance`
--
ALTER TABLE `tqk_balance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_basklist`
--
ALTER TABLE `tqk_basklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_basklistlogo`
--
ALTER TABLE `tqk_basklistlogo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_brand`
--
ALTER TABLE `tqk_brand`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_brandcate`
--
ALTER TABLE `tqk_brandcate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_finance`
--
ALTER TABLE `tqk_finance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_help`
--
ALTER TABLE `tqk_help`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_hongbaodetail`
--
ALTER TABLE `tqk_hongbaodetail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_itemscate`
--
ALTER TABLE `tqk_itemscate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_itemslike`
--
ALTER TABLE `tqk_itemslike`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_itemsorig`
--
ALTER TABLE `tqk_itemsorig`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_link`
--
ALTER TABLE `tqk_link`
  ADD PRIMARY KEY (`id`);
--
-- Indexes for table `tqk_order`
--
ALTER TABLE `tqk_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_robots`
--
ALTER TABLE `tqk_robots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_setting`
--
ALTER TABLE `tqk_setting`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `tqk_user`
--
ALTER TABLE `tqk_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tqk_usercash`
--
ALTER TABLE `tqk_usercash`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uid` (`uid`) USING BTREE,
  ADD KEY `idx_type` (`type`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tqk_admin`
--
ALTER TABLE `tqk_admin`
  MODIFY `id` mediumint(6) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `tqk_adminrole`
--
ALTER TABLE `tqk_adminrole`
  MODIFY `id` tinyint(3) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tqk_apply`
--
ALTER TABLE `tqk_apply`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tqk_articlecate`
--
ALTER TABLE `tqk_articlecate`
  MODIFY `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `tqk_balance`
--
ALTER TABLE `tqk_balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tqk_basklist`
--
ALTER TABLE `tqk_basklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tqk_basklistlogo`
--
ALTER TABLE `tqk_basklistlogo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tqk_brand`
--
ALTER TABLE `tqk_brand`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT for table `tqk_brandcate`
--
ALTER TABLE `tqk_brandcate`
  MODIFY `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `tqk_finance`
--
ALTER TABLE `tqk_finance`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tqk_help`
--
ALTER TABLE `tqk_help`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tqk_hongbaodetail`
--
ALTER TABLE `tqk_hongbaodetail`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tqk_itemscate`
--
ALTER TABLE `tqk_itemscate`
  MODIFY `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `tqk_itemslike`
--
ALTER TABLE `tqk_itemslike`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tqk_itemsorig`
--
ALTER TABLE `tqk_itemsorig`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tqk_link`
--
ALTER TABLE `tqk_link`
  MODIFY `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `tqk_order`
--
ALTER TABLE `tqk_order`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tqk_robots`
--
ALTER TABLE `tqk_robots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `tqk_user`
--
ALTER TABLE `tqk_user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `tqk_usercash`
--
ALTER TABLE `tqk_usercash`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER  TABLE  `tqk_brand`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_brand`  ADD  INDEX idx_ordid (`ordid`);
ALTER  TABLE  `tqk_brand`  ADD  INDEX idx_cate_id (`cate_id`);
ALTER  TABLE  `tqk_brandcate`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_order`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_user`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_usercash`  ADD  INDEX idx_id (`id`);
ALTER  TABLE  `tqk_order`  ADD  INDEX idx_oid (`oid`);
ALTER  TABLE  `tqk_user`  ADD  INDEX idx_oid (`oid`);
ALTER TABLE `tqk_itemscate` ADD INDEX idx_id (`id`);
ALTER  TABLE  `tqk_order`  ADD  INDEX idx_relation_id (`relation_id`);
ALTER  TABLE  `tqk_user`  ADD  INDEX idx_webmaster_pid (`webmaster_pid`);
ALTER  TABLE  `tqk_pddorder`  ADD  INDEX idx_id (`id`);
ALTER TABLE `tqk_robots` CHANGE `tb_cid` `tb_cid` VARCHAR(200) NULL DEFAULT NULL COMMENT '淘宝网分类ID';
update `tqk_robots` set `tb_cid`=`keyword`, `recid`=`cate_id`;
update `tqk_robots` set `keyword`='';