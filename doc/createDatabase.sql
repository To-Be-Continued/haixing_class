create database if not exists `haixing`;

/**
*用户基本信息表
*/
create table if not exists `users_1`
(
	`u_id` 				bigint auto_increment primary key not null,     /*用户ID*/
    `u_iden` 			char(18),                                       /*身份证*/
    `u_tel` 			char(11) not null,                              /*手机号*/
    `openid`            varchar(100) not null,                          /*小程序ID*/
    `u_email` 			varchar(20),                                    /*邮箱*/
    `u_qq`				varchar(20),                                    /*QQ*/
    `u_pwd`				varchar(32) not null,                           /*密码*/
    `u_name`			varchar(10),                                    /*姓名*/
    `u_sex`				char(1),                                        /*性别*/
    `u_birth`			Date,                                           /*生日*/
    `u_sch`				varchar(20),                                    /*学校*/
    `u_major`			varchar(20),                                    /*专业*/
    `u_isiden`			boolean default '0',                            /*是否身份认证*/
    /**
    * 0 未认证 1 认证中 2 认证成功 3 认证失败
    */
    `u_isseller`		boolean default '1',                            /*是否卖家认证*/
    /**
    *0 未认证 1 已认证
    */
    `u_age`            int,                                              /*年龄*/
    `u_authenticate`   varchar(500),                                     /*认证照片*/
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*用户变更信息表
*/
create table if not exists `users_2`
(
	`u_id`				bigint primary key not null,                      /*用户ID*/
    `u_nickname`		varchar(100),                                     /*昵称*/
    `u_intro`			varchar(30),                                      /*简介*/
    `u_level`			int default '0',                                  /*等级*/
    `u_point`			int default '0',                                  /*积分*/
    `u_credit`			int default '0',                                  /*信誉*/
    `u_imgpath`			varchar(500),                                     /*头像*/
    `u_learnedlen`		int default '0',                                  /*学习时长*/
    `u_learnedsum`		int default '0',                                  /*学习课程数*/
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade 
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*卖家信息表
*/
create table if not exists `users_3`
(
	`u_id`				bigint primary key not null,
    `u_coucsr`			int default '0',                                   /*客户满意度*/
    `u_cousum`			int default '0',                                   /*发布课程数*/
    `u_cousales`		int default '0',                                   /*总销量*/
    `u_coulen`			int default '0',                                   /*授课时长*/
    `u_fans`			int default '0',                                   /*粉丝数*/
    `u_money`           int default '0',                    
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*用户后端权限表
*/
create table if not exists `users_power`
(
    `u_id`              bigint primary key not null,                          /*用户ID*/
    `p_ucheck`          boolean default '0',                                  /*课程审核权限*/
    `p_ccheck`          boolean default '0',                                  /*用户审核权限*/
    `p_push`            boolean default '0',                                  /*推送消息权限*/
    `p_add`             boolean default '0',                                  /*增加管理员权限*/
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*用户信息权限表
*/
create table if not exists `users_setting`
(
	`u_id`				bigint primary key not null,                            /*用户ID*/ 
	`f_age`				boolean default '1',                                    /*是否显示年龄*/ 
    `f_sch`				boolean default '1',                                    /*是否显示学校*/ 
    `f_name`			boolean default '1',                                    /*是否显示姓名*/ 
    `f_major`			boolean default '1',                                    /*是否显示专业*/ 
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
 *用户关注专业表
 */ 
create table if not exists `major_follows`
(
	`m_id`				bigint primary key auto_increment not null,              /*关注ID*/ 
    `sys_mid`			bigint,                                                  /*专业ID*/ 
    `u_id`				bigint,                                                  /*用户ID*/ 
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*签到表
*/
create table if not exists `signs`
(
	`sign_id`			bigint auto_increment primary key not null,                /*签到ID*/ 
    `u_id`				bigint,                                                    /*用户ID*/ 
    `sign_time`			timestamp default current_timestamp,                       /*签到时间*/
    `sign_num`          int default '0',                                            /*签到次数*/  
    foreign key(u_id)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*课程基础信息表
*/
create table if not exists `courses_1`
(
	`c_id`				bigint auto_increment primary key not null,                  /*课程ID*/ 
    `c_name`		    varchar(20),                                                 /*课程名*/ 
    `c_major`			varchar(20),                                                 /*专业*/ 
    `c_intro`			varchar(20),                                                 /*介绍*/ 
    `c_detail`			varchar(100),                                                /*详情*/ 
    `c_imgpath`			varchar(500),                                                /*封面*/ 
    `c_releaseid`		bigint,                                                      /*发布者ID*/ 
    foreign key(c_releaseid)references users_1(u_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*课程更新信息表
*/
create table if not exists `courses_2`
(
	`c_id`				bigint primary key not null,                                /*课程ID*/ 
    `c_time` 			varchar(100),                                               /*时间*/ 
    `c_place` 			varchar(50),                                                /*地点*/ 
    `c_price` 			int default '0',                                            /*价格*/ 
    `c_len`				int default '0',                                            /*时长*/ 
    `c_star`			int default '0',                                            /*星级*/ 
    `c_love`			int default '0',                                            /*收藏人数*/ 
    `c_purchase`		int default '0',                                            /*购买人数*/ 
    `c_state`			int default '0',                                            /*状态*/ 
     /* 0 审核中 1 已审核 2 已购买 3 已下架 4 审核失败 5 已删除 */
    foreign key(c_id)references courses_1(c_id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*购物车
*/
create table if not exists `shopping_carts`
(
	`sp_id`				bigint primary key auto_increment not null,                 /*购物车ID*/ 
    `u_id`				bigint,                                                     /*购买者ID*/ 
    `c_id`				bigint,                                                     /*课程ID*/ 
    `c_num`				int default '0',                                            /*数量*/ 
    foreign key(u_id)references users_1(u_id)on delete cascade on update cascade,
    foreign key(c_id)references courses_1(c_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*订单表
*/
create table if not exists `orders`
(
	`order_id`			bigint auto_increment primary key not null,                /*订单ID*/ 
    `order_time`		timestamp default current_timestamp,                       /*下单时间*/ 
    `purchase_time`		timestamp,                                                 /*购买时间*/ 
    `order_money`		int default '0',                                           /*订单金额*/ 
    `order_state`		int default '0',                                           /*订单状态*/ 
    /*0 未付款 1 已付款 2 已确认 3待评价 4 完成交易5 退款中 6 已退款 7 已取消 8 已删除*/
    `c_id`				bigint,                                                    /*课程ID*/ 
    `u_id`				bigint,                                                    /*购买者ID*/ 
    `c_num`             int default '0',                                           /*用户ID*/ 
    `c_releaseid`       bigint,                                                    /*发布者ID*/ 
    foreign key(c_id)references courses_1(c_id)on delete cascade on update cascade,
    foreign key(u_id)references users_1(u_id)on delete cascade on update cascade,
    foreign key(c_releaseid)references users_1(u_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*评论表
*/	
create table if not exists `comments`
(
	`com_id`			bigint auto_increment primary key not null,              /*评论ID*/ 
    `com_text` 			varchar(50),                                             /*内容*/ 
    `com_like`			int default '0',                                         /*点赞数*/ 
    `com_star`			int default '0',                                         /*评论星级*/ 
    `com_time`          timestamp default current_timestamp,                     /*评论时间*/ 
    `c_id`				bigint,                                                  /*课程ID*/
    `u_id`				bigint,                                                  /*用户ID*/
    foreign key(c_id)references courses_1(c_id)on delete cascade on update cascade,
    foreign key(u_id)references users_1(u_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*粉丝表
*/	
create table if not exists `fans`
(
	`fan_id`			bigint auto_increment primary key not null,             /*粉丝ID*/
    `fan_from`			bigint,                                                 /*关注者*/
    `fan_to`			bigint,                                                 /*被关注者*/
    foreign key(fan_from)references users_1(u_id)on delete cascade on update cascade,
    foreign key(fan_to)references users_1(u_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*标签表
*/
create table if not exists `tags`
(
	`tag_id`			bigint auto_increment primary key not null,             /*标签ID*/
    `tag_text`			varchar(20),                                            /*标签内容*/
    `c_id`				bigint,                                                 /*课程ID*/
    foreign key(c_id)references courses_1(c_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*系统标签表
*/
create table if not exists `sys_tags`
(
	`sys_tagid`			bigint auto_increment primary key not null,
    `sys_tagtext`		varchar(20),                                             /*系统标签内容*/
    `sys_tagsearch`		int default '0'                                          /*标签搜索量*/
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*专业表
*/
create table if not exists `sys_major`
(
	`sys_mid`			bigint auto_increment primary key not null,
    `sys_mtext`			varchar(20),                                             /*专业名*/
    `sys_msearch`		int default '0',                                         /*专业搜索量*/
    `sys_mfollows`		int default '0',                                         /*关注量*/
    `sys_mcourses`  	int default '0'                                          /*课程数量*/
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*token表
*/
create table if not exists `sys_token`
(
	`token_id` 			bigint auto_increment primary key not null,              /*tokenID*/          
    `token`				varchar(100) not null,                                   /*token*/
    `last_visit`		timestamp default current_timestamp,                     /*最后访问时间*/
    `u_id`				bigint not null,                                         /*用户ID*/
    foreign key(u_id)references users_1(u_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*点赞表
*/
create table if not exists `thumbsup`
(
    `th_id`            bigint auto_increment primary key not null,              /*点赞ID*/
    `com_id`           bigint,                                                  /*评论ID*/
    `u_id`             bigint,                                                  /*用户ID*/
    foreign key(u_id)references users_1(u_id)on delete cascade on update cascade,
    foreign key(com_id)references comments(com_id)on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
*图片轮播表
*/
create table if not exists `picturerotation`
(
    `pic_id`           bigint auto_increment primary key not null,             /*图片ID*/
    `pic_name`         varchar(50),                                            /*名称*/
    `pic_path`         varchar(500),                                           /*图片路径*/
    `pic_ad`           varchar(500)                                            /*广告连接路径*/
)ENGINE=InnoDB DEFAULT CHARSET=utf8;










