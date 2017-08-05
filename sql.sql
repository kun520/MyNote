CREATE DATABASE myNote default charset utf8;
use myNote;

create table s_note(
	id mediumint unsigned not null auto_increment comment 'Id',
	title varchar(100) not null comment '标题',
	content longtext not null comment '内容',
	addtime datetime not null /*default current_timestamp*/ comment '添加时间',
	img_path varchar(100) comment '图片地址',
	img_path_big varchar(100) comment '大缩略图片地址',
	img_path_mid varchar(100) comment '中缩略图片地址',
	img_path_sm varchar(100) comment '小缩略图片地址',
	ip int not null comment 'Ip地址',
	primary key(id)
)engine=InnoDB default charset utf8 comment '留言表';

/*
存IP的知识点
ip char(15) --> 192.168.111.111
	优点: 直接使用,直观看出是ip格式
	缺点: 更占用硬盘空间,每个ip占45字节(15*3)
ip int      --> 2434234325
	优点: 节省空间 ,每个ip占4个字节
	缺点: 存和显示时要转换一下
	如何转: ip2long --> ip转数字
		   long2ip --> 数字转ip
*/