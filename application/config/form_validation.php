<?php
$config = array(
	'register' => array(
		array(
			'field' => 'u_tel',
			'label' => '手机号',
			'rules' => 'required|max_length[11]'
		),
		array(
			'field' => 'u_pwd',
			'label' => '密码',
			'rules' => 'required|max_length[32]'
		)
	),
	'wechatregister' => array(
		array(
			'field' => 'code',
			'label' => 'code',
			'rules' => 'required'
		),
		array(
			'field' => 'u_tel',
			'label' => '手机号',
			'rules' => 'required|max_length[11]'
		),
		array(
			'field' => 'u_pwd',
			'label' => '密码',
			'rules' => 'required|max_length[32]'
		)
	),
	'login' => array(
		array(
			'field' => 'u_tel',
			'label' => '手机号',
			'rules' => 'required|max_length[11]'
		),
		array(
			'field' => 'u_pwd',
			'label' => '密码',
			'rules' => 'required|max_length[32]'
		)
	),
	'cou_info' => array(
		array(
			'field' => 'c_name',
			'label' => '课程名称',
			'rules' => 'required|max_length[20]'
		),
		array(
			'field' => 'c_major',
			'label' => '课程专业',
			'rules' => 'required|max_length[20]'
		),
		array(
			'field' => 'c_detail',
			'label' => '课程详情',
			'rules' => 'required|max_length[20]'
		),
		array(
			'field' => 'c_price',
			'label' => '课程价格',
			'rules' => 'required'
		),
		array(
			'field' => 'c_time',
			'label' => '课程时间',
			'rules' => 'required'
		),
		array(
			'field' => 'c_place',
			'label' => '课程地点',
			'rules' => 'required|max_length[50]'
		)
	),
	'cou_shopping' => array(
		array(
			'field' => 'c_id',
			'label' => '课程ID',
			'rules' => 'required'
		),
		array(
			'field' => 'c_num',
			'label' => '课程数量',
			'rules' => 'required'
		)
	),
	'cart_enter' => array(
		array(
			'field' => 'sp_id',
			'label' => '购物车ID',
			'rules' => 'required'
		)
	),
	'move_cart' => array(
		array(
			'field' => 'sp_id',
			'label' => '购物车ID',
			'rules' => 'required'
		)
	),
	'cou_order' => array(
		array(
			'field' => 'c_id',
			'label' => '课程ID',
			'rules' => 'required'
		),
		array(
			'field' => 'c_num',
			'label' => '购买数量',
			'rules' => 'required'
		),
		array(
			'field' => 'order_money',
			'label' => '课程总金额',
			'rules' => 'required'
		)
	),
	'entercou' => array(
		array(
			'field' => 'c_id',
			'label' => '课程ID',
			'rules' => 'required'
		)
	),
	'get_tel' => array(
		array(
			'field' => 'code',
			'label' => 'code',
			'rules' => 'required'
		),
		array(
			'field' => 'encryptedData',
			'label' => '加密数据',
			'rules' => 'required'
		),
		array(
			'field' => 'iv',
			'label' => '初始向量',
			'rules' => 'required'
		)
	),
	'cou_examine' => array(
		array(
			'field' => 'c_id',
			'label' => '课程ID',
			'rules' => 'required'
		)
	),
	'cou_del' => array(
		array(
			'field' => 'c_id',
			'label' => '课程ID',
			'rules' => 'required'
		)
	),
	'cou_accept' => array(
		array(
			'field' => 'order_id',
			'label' => '订单ID',
			'rules' => 'required'
		)
	),
	'cou_buy' => array(
		array(
			'field' => 'order_id',
			'label' => '订单ID',
			'rules' => 'required'
		)
	),
	'cou_edit' => array(
		array(
			'field' => 'c_id',
			'label' => '课程ID',
			'rules' => 'required'
		),
		array(
			'field' => 'c_major',
			'label' => '课程专业',
			'rules' => 'required|max_length[20]'
		),
		array(
			'field' => 'c_detail',
			'label' => '课程详情',
			'rules' => 'required|max_length[20]'
		),
		array(
			'field' => 'c_price',
			'label' => '课程价格',
			'rules' => 'required'
		),
		array(
			'field' => 'c_time',
			'label' => '课程时间',
			'rules' => 'required'
		),
		array(
			'field' => 'c_place',
			'label' => '课程地点',
			'rules' => 'required|max_length[50]'
		)
	),
	'user_setting' => array(
		array(
			'field' => 'f_age',
			'label' => '年龄选项',
			'rules' => 'required'
		),
		array(
			'field' => 'f_sch',
			'label' => '学校选项',
			'rules' => 'required'
		),
		array(
			'field' => 'f_name',
			'label' => '姓名选项',
			'rules' => 'required'
		),
		array(
			'field' => 'f_major',
			'label' => '专业选项',
			'rules' => 'required'
		)
	),
	'comment' => array(
		array(
			'field' => 'c_id',
			'label' => '课程ID',
			'rules' => 'required'
		),
		array(
			'field' => 'order_id',
			'label' => '订单ID',
			'rules' => 'required'
		),
		array(
			'field' => 'com_text',
			'label' => '评价内容',
			'rules' => 'required|max_length[50]'
		),
		array(
			'field' => 'com_star',
			'label' => '评价星级',
			'rules' => 'required'
		),
	),
	'user_info' => array(
		array(
			'field' => 'u_nickname',
			'label' => '昵称',
			'rules' => 'required'
		),
		array(
			'field' => 'u_intro',
			'label' => '介绍',
			'rules' => 'required'
		),
		array(
			'field' => 'u_email',
			'label' => '邮箱',
			'rules' => 'required'
		),
		array(
			'field' => 'u_qq',
			'label' => 'QQ',
			'rules' => 'required'
		),
		array(
			'field' => 'u_sex',
			'label' => '性别',
			'rules' => 'required'
		),
		array(
			'field' => 'u_birth',
			'label' => '生日',
			'rules' => 'required'
		),
		array(
			'field' => 'u_sch',
			'label' => '学校',
			'rules' => 'required'
		),
		array(
			'field' => 'u_major',
			'label' => '专业',
			'rules' => 'required'
		),
	),
	'thumbup' => array(
		array(
			'field' => 'com_id',
			'label' => '评论ID',
			'rules' => 'required'
		)
	),
	'evaluate' => array(
			array(
			'field' => 'order_id',
			'label' => '订单ID',
			'rules' => 'required'
		)
	)
);
?>