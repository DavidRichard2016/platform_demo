<?php
return array(
                //'配置项'=>'配置值'
//				'SHOW_PAGE_TRACE' =>true, // 显示页面Trace信息
				
                'URL_MODEL' => '2', //URL模式
                'DB_TYPE' => 'mysql',
                'DB_HOST' => '127.0.0.1',
                'DB_NAME' => 'test',
                'DB_USER' => 'root',
                'DB_PWD' => '889136',
                'DB_PORT' => '3306',
                'DB_PREFIX' => '',
				'DB_CHARSET' => 'utf8', 
                'TMPL_PARSE_STRING' => array(
                        '__JS__' => '/Public/Js', //JS目录
                        '__CSS__' => '/Public/Css', //样式目录
                        '__IMG__' => '/Public/Img', //图片目录
                        '__THM__' => '/Public/Themes', //主题目录
                        '__JSON__' => '/Public/Json', //Json文件目录
                        '__APP__' => strip_tags(_PHP_FILE_),
                ),


);
?>