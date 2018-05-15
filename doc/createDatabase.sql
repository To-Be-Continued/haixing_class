create database if not exists `haixing`;

create table if not exists `users_1`
(
	`u_id` 				bigint auto_increment primary key not null,
    `u_iden` 			char(18),
    `u_tel` 			char(11) not null,
    `u_email` 			varchar(20),
    `u_qq`				varchar(20),
    `u_pwd`				varchar(32) not null,
    `u_name`			varchar(10),
    `u_sex`				char(1),
    `u_birth`			Date,
    `u_sch`				varchar(20),
    `u_major`			varchar(20),
    `u_isiden`			boolean default '0',
    `u_isseller`		boolean default '0'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `users_2`
(
	`u_id`				bigint primary key not null,
    `u_nickname`		varchar(10),
    `u_intro`			varchar(30),
    `u_level`			int,
    `u_point`			int,
    `u_credit`			int,
    `u_imgpath`			varchar(100),
    `u_learnedlen`		int,
    `u_learnedsum`		int,
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade 
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `users_3`
(
	`u_id`				bigint primary key not null,
    `u_coucsr`			int,
    `u_cousum`			int,
    `u_cousales`		int,
    `u_coulen`			int,
    `u_fans`			int,
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `users_setting`
(
	`u_id`				bigint primary key not null,
	`f_age`				boolean default '1',
    `f_sch`				boolean default '1',
    `f_name`			boolean default '1',
    `f_major`			boolean default '1',
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `major_follows`
(
	`m_id`				bigint primary key auto_increment not null,
    `m_text`			varchar(20),
    `u_id`				bigint,
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `signs`
(
	`sign_id`			bigint auto_increment primary key not null,
    `u_id`				bigint,
    `sign_time`			timestamp default current_timestamp,
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `courses_1`
(
	`c_id`				bigint auto_increment primary key not null,
    `c_name`		    varchar(20),
    `c_major`			varchar(20),
    `c_intro`			varchar(20),
    `c_detail`			varchar(100),
    `c_imgpath`			varchar(100),
    `c_releaseid`		bigint,
    foreign key(c_releaseid)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `courses_2`
(
	`c_id`				bigint primary key not null,
    `c_time` 			timestamp,
    `c_place` 			varchar(50),
    `c_price` 			int,
    `c_len`				int,
    `c_star`			int,
    `c_love`			int,
    `c_purchase`		int,
    `c_state`			int default '0',
     /* 0 审核中 1 已审核 2 已购买 3 已下架 4 审核失败 */
    foreign key(c_id)references courses_1(c_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `shopping_carts`
(
	`sp_id`				bigint primary key auto_increment not null,
    `u_id`				bigint,
    `c_id`				bigint,
    `c_num`				int,
    foreign key(u_id)references users_1(u_id)on delete cascade on update cascade,
    foreign key(c_id)references courses_1(c_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `orders`
(
	`order_id`			bigint auto_increment primary key not null,
    `order_time`		timestamp,
    `purchase_time`		timestamp,
    `order_money`		int,
    `order_state`		int default '0',
    /*0 未付款 1 已付款 2 已确认 3待评价 4 完成交易5 退款中 6 已退款*/
    `c_id`				bigint,
    `u_id`				bigint,
    foreign key(c_id)references courses_1(c_id)on delete cascade on update cascade,
    foreign key(u_id)references users_1(u_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
create table if not exists `ecomments`
(
	`com_id`			bigint auto_increment primary key not null,
    `com_text` 			varchar(50),
    `com_like`			int,
    `com_star`			int,
    `c_id`				bigint,
    `u_id`				bigint,
    foreign key(c_id)references courses_1(c_id)on delete cascade on update cascade,
    foreign key(u_id)references users_1(u_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
create table if not exists `fans`
(
	`fan_id`			bigint auto_increment primary key not null,
    `fan_from`			bigint,
    `fan_to`			bigint,
    foreign key(fan_from)references users_1(u_id)on delete cascade on update cascade,
    foreign key(fan_to)references users_1(u_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `tags`
(
	`tag_id`			bigint auto_increment primary key not null,
    `tag_text`			varchar(20),
    `c_id`				bigint,
    foreign key(c_id)references courses_1(c_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `sys_tags`
(
	`sys_tagid`			bigint auto_increment primary key not null,
    `sys_tagtext`		varchar(20),
    `sys_tagsearch`		int
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `sys_major`
(
	`sys_mid`			bigint auto_increment primary key not null,
    `sys_mtext`			varchar(20),
    `sys_msearch`		int,
    `sys_mfollows`		int,
    `sys_mcourses`  	int
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `sys_token`
(
	`token_id` 			bigint auto_increment primary key not null,
    `token`				varchar(100) not null,
    `last_visit`		timestamp default current_timestamp,
    `u_id`				bigint not null,
    foreign key(u_id)references users_1(u_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

	










