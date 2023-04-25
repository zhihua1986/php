<?php
return array (
  'HTML_CACHE_TIME' => 60,
  'HTML_FILE_SUFFIX' => '',
  'DEFAULT_MODULE'        =>   'Home',
  'HTML_CACHE_RULES' => 
   array (
    'index:index' =>
    array (
      0 => '{:module}{:controller}_{:action}',
      1 => 1800,
    ),
    'xinping:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_s{sort}p_{p}',
      1 => 1800,
    ),
    'cate:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_s{sort}_st{stype}_c{cid}_s{sid}_p{p}_k{k|md5}',
      1 => 1800,
    ),
    'item:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_{id}',
      1 => 1800,
    ),
    'pdditem:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_{id|md5}',
      1 => 1800,
    ),
    'pdd:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_s{sort}_c{cid}_p{p}_k{k|md5}',
      1 => 1800,
    ),
    'jditem:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_{id}',
      1 => 1800,
    ),
    'jd:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_s{sort}_c{cid}_p{p}_st{stype}_po{pop}_k{k|md5}',
      1 => 1800,
    ),
    'ershi:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_s{sort}p_{p}',
      1 => 1800,
    ),
    'top100:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_s{sort}p_{p}',
      1 => 1800,
    ),
    'jingxuan:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_s{sort}_p{p}',
      1 => 1800,
    ),
    'jiu:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_s{sort}_p{p}',
      1 => 1800,
    ),
	'chaohuasuan:index' =>
	array (
	  0 => '{:module}_{:controller}_{:action}_s{sort}_p{p}',
	  1 => 1800,
	),
    'article:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_c{cateid}_p{p}_{id}',
      1 => 1800,
    ),
    'article:read' => 
    array (
      0 => '{:module}_{:controller}_{:action}_{id}',
      1 => 3600,
    ),
    'brand:index' => 
    array (
      0 => '{:module}_{:controller}_{:action}_c{cateid}_p{p}',
      1 => 3600,
    ),

  ),
    'URL_ROUTE_RULES' =>
    array (
	'view/:id'  => 'view/index',
    '/^index$/' => 'index',
    '/^index\/so\/k\/(.*?)\/p\/(\d+)$/' => 'index/so?k=:1&p=:2',
    '/^index\/search\/key\/(.*?)\/p\/(\d+)$/' => 'index/search?key=:1&p=:2',
    '/^index\/index\/sort\/(\w+)$/' => 'index/index?&sort=:1',
    '/^index\/index\/sort\/(\w+)\/p\/(\d+)$/' => 'index/index?sort=:1&p=:2',
    '/^cate\/sort\/(\w+)\/cid\/(\d+)\/sid\/(\d+)$/' => 'cate/index?sort=:1&cid=:2&sid=:3',
	'/^cate\/sort\/(\w+)\/cid\/(\d+)\/sid\/(\d+)\/coupon\/(\d+)$/' => 'cate/index?sort=:1&cid=:2&sid=:3&coupon=:4',
    '/^cate\/stype\/(\d+)\/sort\/(\w+)\/cid\/(\d+)\/sid\/(\d+)$/' => 'cate/index?stype=:1&sort=:2&cid=:3&sid=:4',
	'/^cate\/stype\/(\d+)\/sort\/(\w+)\/cid\/(\d+)\/sid\/(\d+)\/coupon\/(\d+)$/' => 'cate/index?stype=:1&sort=:2&cid=:3&sid=:4&coupon=:5',
    '/^cate\/cid\/(\d+)\/stype\/(\d+)\/sid\/(\d+)$/' => 'cate/index?cid=:1&stype=:2&sid=:3',
    '/^cate\/cid\/(\d+)\/stype\/(\d+)\/sid\/(\d+)\/coupon\/(\d+)$/' => 'cate/index?cid=:1&stype=:2&sid=:3&coupon=:4',
    '/^cate\/sort\/(\w+)\/stype\/(\d+)\/cid\/(\d+)\/sid\/(\d+)$/' => 'cate/index?sort=:1&stype=:2&cid=:3&sid=:4',
	 '/^cate\/sort\/(\w+)\/stype\/(\d+)\/cid\/(\d+)\/sid\/(\d+)\/coupon\/(\d+)$/' => 'cate/index?sort=:1&stype=:2&cid=:3&sid=:4&coupon=:5',
    '/^cate\/cid\/(\d+)\/stype\/(\d+)$/' => 'cate/index?cid=:1&stype=:2',
    '/^cate\/cid\/(\d+)\/sort\/(\w+)$/' => 'cate/index?cid=:1&sort=:2',
    '/^cate\/sort\/(\w+)$/' => 'cate/index?sort=:1',
    '/^index\/p\/(\d+)$/' => 'index/index?p=:1',
    '/^cate\/cid\/(\d+)\/sid\/(\d+)$/' => 'cate/index?cid=:1&sid=:2',
    '/^cate\/cid\/(\d+)$/' => 'cate/index?cid=:1',
    '/^cate\/k\/(.*?)$/' => 'cate/index?k=:1',
    '/^shortcut\/$/' => 'index/shortcut',
    '/^chaxun\/$/' => 'chaxun/index',
    '/^score\/$/' => 'score/index',
    '/^sou\/$/' => 'sou/index',
    'sou/:id' => 'sou/index',
    '/^comment\/$/' => 'comment/index',
    '/^comment\/p\/(\d+)$/' => 'comment/index?p=:1',
    '/^baoming$/' => 'baoming/shenhe',
    '/^baoming\/p\/(\d+)$/' => 'baoming/my?p=:1',
    '/^inval\/(\d+)$/' => 'inval/index?id=:1',
    //'/^help\/(\d+)$/' => 'help/read?id=:1',
	'help/:id'  => 'help/index',
    '/^user\/register$/' => 'user/register',
    '/^user\/login$/' => 'user/login',
    '/^user\/logout$/' => 'user/logout',
    '/^union$/' => 'union/index',
    '/^gift$/' => 'gift/index',
    '/^gift\/p\/(\d+)$/' => 'gift/index?p=:1',
    '/^gift\/cid\/(\d+)$/' => 'gift/index?cid=:1',
    '/^gift\/item\/(\d+)$/' => 'gift/detail?id=:1',
    '/^space$/' => 'space/index',
    '/^space\/(\d+)$/' => 'space/index?uid=:1',
    '/^article\/cateid\/(\d+)$/' => 'article/index?cateid=:1',
    '/^article\/view_(\d+)$/' => 'article/read?id=:1',
    '/^jump\/item\/(.*?)$/' => 'jump/index?iid=:1',
    '/^jump\/(.*?)$/' => 'jump/index?id=:1',
    '/^jump\/$/' => 'jump/index',
    '/^list\/$/' => 'list/index',
	 '/^special\/id\/(\d+)$/' => 'special/index?id=:1',
    '/^list\/p\/(\d+)$/' => 'list/index?p=:1',
    '/^item\/id\/(.*?)$/' => 'item/index?id=:1',
    '/^item\/iid\/(.*?)$/' => 'item/index?iid=:1',
    '/^pdditem\/id\/(\d+)$/' => 'pdditem/index?id=:1',
	'/^pdditem\/jump\/(.*?)$/' => 'pdditem/jump?id=:1',
	'/^pdditem\/s\/(.*?)$/' => 'pdditem/index?s=:1',
    '/^pdd\/cid\/(\d+)$/' => 'pdd/index?cid=:1',
    '/^pdd\/sort\/(\w+)\/cid\/(\d+)$/' => 'pdd/index?sort=:1&cid=:2',
	 '/^pdd\/stype\/(\d+)\/sort\/(\w+)\/cid\/(\d+)$/' => 'pdd/index?stype=:1&sort=:2&cid=:3',
    '/^pdditem\/iid\/(\d+)$/' => 'pdditem/index?iid=:1',
    '/^jditem\/id\/(\d+)$/' => 'jditem/index?id=:1',
    '/^jd\/cid\/(\d+)$/' => 'jd/index?cid=:1',
    '/^jd\/sort\/(\w+)\/cid\/(\d+)$/' => 'jd/index?sort=:1&cid=:2',
    '/^jd\/stype\/(\d+)\/sort\/(\w+)\/cid\/(\d+)$/' => 'jd/index?stype=:1&sort=:2&cid=:3',
    '/^jd\/pop\/(\d+)\/sort\/(\w+)\/cid\/(\d+)$/' => 'jd/index?pop=:1&sort=:2&cid=:3',
     '/^jd\/sort\/(\w+)\/cid\/(\d+)\/pop\/(\d+)$/' => 'jd/index?sort=:1&cid=:2&pop=:3',
     '/^jd\/sort\/(\w+)\/cid\/(\d+)\/stype\/(\d+)$/' => 'jd/index?sort=:1&cid=:2&stype=:3',
	 '/^jd\/k\/(.*?)$/' => 'jd/index?k=:1',
    '/^jiu\/sort\/(\w+)$/' => 'jiu/index?sort=:1',
    '/^jiu\/stype\/(\d+)\/sort\/(\w+)$/' => 'jiu/index?stype=:1&sort=:2',
	'/^chaohuasuan\/sort\/(\w+)$/' => 'chaohuasuan/index?sort=:1',
	'/^chaohuasuan\/stype\/(\d+)\/sort\/(\w+)$/' => 'chaohuasuan/index?stype=:1&sort=:2',
     '/^top100\/sort\/(\w+)\/cid\/(\d+)$/' => 'top100/index?sort=:1&cid=:2',
    '/^top100\/stype\/(\d+)\/sort\/(\w+)\/cid\/(\d+)$/' => 'top100/index?stype=:1&sort=:2&cid=:3',
    '/^pdditem\/iid\/(\d+)$/' => 'pdditem/index?iid=:1',
    '/^detail\/id\/(.*?)$/' => 'detail/index?id=:1',
    '/^detail\/iid\/(.*?)$/' => 'detail/index?iid=:1',
    '/^out\/action\/(.*?)\/id\/(.*?)$/' => 'out/index?action=:1&id=:2',
    '/^m\/cate\/cid\/(\d+)\/sort\/(\w+)$/' => 'index/cate?cid=:1&sort=:2',
    '/^m\/cate\/cid\/(\d+)\/sort\/(\w+)\/p\/(\d+)$/' => 'index/cate?cid=:1&sort=:2&p=:3',
    '/^m\/detail\/id\/(.*?)$/' => 'm/detail/index?id=:1',
    '/^brand\/cateid\/(\d+)$/' => 'brand/index?cateid=:1',
  ),



);
