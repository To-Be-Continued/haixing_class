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
			'field' => 'c_intro',
			'label' => '课程介绍',
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
			'field' => 'c_len',
			'label' => '课程时长',
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
	)
);
?>