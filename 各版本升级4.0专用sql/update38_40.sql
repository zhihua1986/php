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