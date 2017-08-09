CREATE DATABASE myNote default charset utf8;
use myNote;

create table s_note(
	id mediumint unsigned not null auto_increment comment 'Id',
	title varchar(100) not null comment '标题',
	content longtext not null comment '内容',
	addtime datetime not null /*default current_timestamp*/ comment '添加时间',
	ip int not null comment 'Ip地址',
	primary key(id)
)engine=InnoDB default charset utf8 comment '留言表';


create table s_note_image(
	id mediumint unsigned not null auto_increment comment 'Id',
	note_id mediumint unsigned not null comment '该图片所属的留言Id',
	image varchar(100) comment '图片地址',
	big_image varchar(100) comment '大缩略图片地址',
	mid_image varchar(100) comment '中缩略图片地址',
	sm_image varchar(100) comment '小缩略图片地址',
	primary key(id)
)engine=InnoDB default charset utf8 comment '留言图片表';

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




create table s_category(
	id mediumint unsigned not null auto_increment comment 'Id',
	cate_name varchar(32) comment '分类名称',
	parent_id mediumint unsigned not null default '0' comment '所属上级分类id',
	primary key(id)
)engine=InnoDB default charset utf8 comment '分类表';