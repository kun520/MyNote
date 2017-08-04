<?php
return array(
	//mysql的一些设置	
    'DB_TYPE' => 'mysql',       // 数据库类型
    'DB_HOST' => '127.0.0.1',   // 服务器地址
    'DB_NAME' => 'mynote',      // 数据库名
    'DB_USER' => 'root',        // 用户名
    'DB_PWD'  => 'root',        // 密码
    'DB_PORT' => '3306',        // 端口
    'DB_PREFIX' => 's_',        // 数据库表前缀
    
    'TMPL_L_DELIM' => '<{',     // 模板引擎普通标签开始标记
    'TMPL_R_DELIM' => '}>',     // 模板引擎普通标签结束标记
    
    'SHOW_PAGE_TRACE' => true,   // 显示页面Trace信息
    
    //URL的配置
    'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
    'URL_PATHINFO_DEPR'     =>  '-',    // PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX'       =>  'html',  // URL伪静态后缀设置
);