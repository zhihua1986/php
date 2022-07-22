<?php
return array(
    'app_begin' => array(
        'Behavior\CheckLangBehavior'
    ),
	'view_filter' => array(
	        'Behavior\TokenBuildBehavior', // 表单令牌
	 )
);