<?php
return array(
	//mysql的一些设置	
    'DB_TYPE' => 'mysql',       // 数据库类型
    'DB_HOST' => 'localhost',   // 服务器地址
    'DB_NAME' => 'mynote',      // 数据库名
    'DB_USER' => 'root',        // 用户名
    'DB_PWD'  => 'root',        // 密码
    'DB_PORT' => '3306',        // 端口
    'DB_PREFIX' => 's_',        // 数据库表前缀
    
    'TMPL_L_DELIM' => '<{',     // 模板引擎普通标签开始标记
    'TMPL_R_DELIM' => '}>',     // 模板引擎普通标签结束标记
    
    'SHOW_PAGE_TRACE' => true,   // 显示页面Trace信息
);