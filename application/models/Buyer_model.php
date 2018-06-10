<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Buyer_model extends CI_Model{
	/*****************************************************************************************************
	 * 私有工具集
	 *****************************************************************************************************/


	/**********************************************************************************************
	 * 公开工具集
	 **********************************************************************************************/


	/**********************************************************************************************
	 * 业务接口
	 **********************************************************************************************/


	/**
	*take course into shoppingcart
	*/
	public function cou_shopping($form)
	{
		//check token $$ get u_id
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
		}
		$data =array(
			'u_id' => $u_id,
			'c_id' => $form['c_id'],
			'c_num' => $form['c_num']
		);

		//do insert 
		$this->db->insert('shopping_carts',$data);

	}


	/**
	*购物车中课程列表
	*/
	public function get_carts($form)
	{
		//check token $$ get u_id
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
		}

		$where = array('u_id' => $u_id);

		$ret = $this->db->select('sp_id,shopping_carts.c_id,c_num,c_name,c_price,c_imgpath')
						->join('courses_1','shopping_carts.c_id=courses_1.c_id')
						->join('courses_2','shopping_carts.c_id=courses_2.c_id')
						->get_where('shopping_carts',$where)
						->result_array();

		if(empty($ret))
		{
			throw new Exception("购物车无订单", 406);	
		}

		return $ret;
	}


	/**
	*购物车中点击课程进入课程详情
	*/
	public function entercou($form)
	{
		//check token 
		if(isset($token['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$where = array('sp_id' => $form['sp_id']);

		$ret = $this->db->select()
						->join('courses_1','shopping_carts.c_id=courses_1.c_id')
						->join('courses_2','shopping_carts.c_id=courses_2.c_id')
						->get_where('shopping_carts',$where)
						->result_array();

		if(empty($ret))
		{
			throw new Exception('数据库错误',406);
		}

		return $ret;


	}


	/*
	 * move one cart
	 */
	public function move_cart($form)
	{
		if (isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$where = array('sp_id' =>$form['sp_id']);
		//check
		if (! $this->db->select('sp_id')
					   ->where($where)
					   ->get('shopping_carts')
					   ->result_array())
		{
			throw new Exception('数据库错误',406);
		}

		//delete
		$this->db->delete('shopping_carts', $where);
	}


	/*
	 * move batch carts
	 */
	public function move_carts($form)
	{
		if (isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		//check sp_id
		if (! isset($form['sp_id']))
		{
			throw new Exception("购物车ID字段不能为空");
		}

		//object translate into json array
		$arr=json_decode(json_encode($form['sp_id']), true);
		if (empty($arr))
		{
			throw new Exception("至少含有一个购物车ID字段");
		}
		foreach ($arr as $key => $value) 
		{
			$where = array('sp_id' => $value);
			$this->db->delete('shopping_carts', $where);
		}
	}


	/*
	 * 买家下订单未付款
	 */
	public function cou_order($form)
	{
		//config
		$members = array('c_id', 'order_money', 'u_id');

		//check token
		if (isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
			$form['u_id'] = $u_id;
		}

		//check c_id
		if ( ! $this->db->select('c_id')
						->where(array('c_id' => $form['c_id']))
						->get('courses_1')
						->result_array())
		{
			throw new Exception("数据库错误", 406);
		}

		//check state
		$where = array('c_id' => $form['c_id']);
		if (  $this->db->select('c_state')
						->where($where)
						->get('courses_2')
						->result_array()[0]['c_state'] == 1)
		{
			$this->db->insert('orders', filter($form, $members));
			$data = array('c_state' => 2);
			$this->db->update('courses_2', $data, $where);
		}
		else
		{
			throw new Exception("无法购买该课程");
		}
	}


	/*
	 * 买家订单列表
	 */
	public function cou_orderlist($form)
	{
		//check token $$ get u_id
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
		}

		$data = array('order_id', 'order_time', 'order_money', 'order_state', 
				 	  'orders.c_id', 'c_name', 'c_num', 'c_imgpath','c_price');
		$where = array('u_id' => $u_id);
		$ret = $this->db->select($data)
						->join('courses_1','orders.c_id=courses_1.c_id')
						->join('courses_2','orders.c_id=courses_2.c_id')
						->get_where('orders',$where)
						->result_array();

		foreach ($ret as $key => $value) {
			$ret[$key]['u_tel'] =$this->db->select('u_tel')
										  ->where($where)
										  ->get('users_1')
										  ->result_array()[0]['u_tel'];
			$res = $this->db->select('u_nickname')
							->where($where)
							->get('users_2')
							->result_array();
			if (empty($res))
			{
				$ret[$key]['u_nickname']=null;
			}
			else
			{
				$ret[$key]['u_nickname']=$res[0]['u_nickname'];
			}
		}
		return $ret;
	}


	/*
	 * 获取课程详情
	 */
	public function get_cdetail($form)
	{
		//check token
		if (isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$where = array('courses_1.c_id' => $form['c_id']);
		$data = array('c_name', 'c_major', 'c_detail', 'c_imgpath', 'c_releaseid','c_time', 
		 			  'c_place', 'c_price', 'c_len', 'c_star', 'c_love', 'c_purchase', 'c_state');
		$que = $this->db->select($data)
						->join('courses_2','courses_1.c_id=courses_2.c_id')
						->get_where('courses_1',$where)
						->result_array();
		if ( empty($que) )
		{
			throw new Exception("invalid c_id", 406);
		}
		$ret['c_info'] = $que[0];
		$ret['tags'] = $this->db->select('tag_text, tag_id')
								->where(array('c_id' => $form['c_id']))
								->get('tags')
								->result_array();
		return $ret;
	}


	/**
	 * 全部课程列表
	 **/
	public function get_allcou($form)
	{
		//check token
		if (isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$where = array('c_state>='=>1,'c_state<='=>3);
		$data = array('courses_1.c_id', 'c_name', 'c_star','c_time', 
		 			  'c_place', 'c_price', 'c_imgpath');
		$ret = $this->db->select($data)
						->join('courses_2','courses_1.c_id=courses_2.c_id')
						->get_where('courses_1')
						->result_array();
		
		if (empty($ret))
		{
			throw new Exception("暂无课程", 406);
		}
		foreach ($ret as $key => $value) {
			$ret[$key]['tags'] = $this->db->select('tag_text, tag_id')
										  ->where(array('c_id' => $value['c_id']))
										  ->get('tags')
										  ->result_array();
		}

		//return
		return $ret;
	}


	/**
	*卖家付款
	*/
	public function cou_buy($form)
	{
		//check token
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$where = array('order_id' => $form['order_id']);
		//确认订单存在
		$ret = $this->db->select('order_state,c_id')
				->where($where)
				->get('orders')
				->row_array();
		
		if(empty($ret))
		{
			throw new Exception("订单不存在", 406);	
		}

		$w = array('c_id' => $ret['c_id']);

		$r = $this->db->select('c_state')
					->where($w)
					->get('courses_2')
					->row_array();
		if(empty($ret))
		{
			throw new Exception("课程不存在", 406);	
		}

		//do update
		$data = array('order_state' => 1);
		if( $r['c_state'] == 2 && $ret['order_state'] == 0)
		{
			$this->db->set($data)
					->where($where)
					->update('orders');
		}
	}


	/*
	 * 未付款-取消订单
	 */
	public function cou_cancelorder($form)
	{
		//check token
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$this->my_user->check_token($form['token']);
		}

		//check order_id
		if ( ! $ret = $this->db->select('order_state, c_id')
							   ->where(array('order_id' => $form['order_id']))
							   ->get('orders')
							   ->result_array())
		{
			throw new Exception("invalid order_id", 406);
		}

		$state = $this->db->select('c_state')
						  ->where(array('c_id' => $ret[0]['c_id']))
						  ->get('courses_2')
						  ->result_array()[0]['c_state'];
		
		if ($state == 2 && $ret[0]['order_state'] == 0)
		{
			$order_state = array('order_state' => 7);
			$c_state = array('c_state' => 1);
			$where = array('order_id' => $form['order_id']);
			$this->db->update('orders', $order_state, $where);
			$this->db->update('courses_2', $c_state, array('c_id' => $ret[0]['c_id']));
		}
		else
		{
			throw new Exception("无法取消", 406);	
		}
	}


	/*
	 * 删除订单
	 */
	public function cou_delorder($form)
	{
		//check token
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$this->my_user->check_token($form['token']);
		}

		//check order_id
		if ( ! $ret = $this->db->select('order_state, c_id')
							   ->where(array('order_id' => $form['order_id']))
							   ->get('orders')
							   ->result_array())
		{
			throw new Exception("invalid order_id", 406);
		}

		$state = $this->db->select('c_state')
						  ->where(array('c_id' => $ret[0]['c_id']))
						  ->get('courses_2')
						  ->result_array()[0]['c_state'];
		
		if ($state == 1 && $ret[0]['order_state'] == 7)
		{
			$order_state = array('order_state' => 8);
			$c_state = array('c_state' => 1);
			$where = array('order_id' => $form['order_id']);
			$this->db->update('orders', $order_state, $where);
			$this->db->update('courses_2', $c_state, array('c_id' => $ret[0]['c_id']));
		}
		else
		{
			throw new Exception("无法删除", 406);	
		}
	}


	/*
	 * 发布课程评论
	 */
	public function comment($form)
	{
		$members = array('u_id', 'c_id', 'com_text', 'com_star');

		//check token && get u_id
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
		}

		//check if buyer
		$where = array(
			'c_id' => $form['c_id'],
			'order_id' => $form['order_id'],
			'u_id' => $u_id
		);
		if ( ! $ret = $this->db->select('order_state')
							   ->where($where)
							   ->get('orders')
							   ->result_array())
		{
			throw new Exception("invalid user", 406);
		}
		if ($ret[0]['order_state'] != 3)
		{
			throw new Exception("invalid order_state", 406);
		}
		$form['u_id'] = $u_id;
		$this->db->insert('comments', filter($form, $members));
		$data = array('order_state'=> 4);
		$this->db->update('orders', $data, $where);
	}


	/*
	 * 获取课程评论列表
	 */
	public function get_list($form)
	{
		$members = array('com_id', 'com_text', 'com_like', 'com_star', 'com_nickname',
						 'com_tel', 'is_like', 'com_imgpath', 'com_time');

		//check token & get user
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
		}

		if (! $ret = $this->db->select('com_id, com_text, com_like, com_star, com_time, u_id')
							  ->where(array('c_id' => $form['c_id']))
							  ->get('comments')
							  ->result_array())
		{
			throw new Exception("invalid c_id", 406);
		}

		foreach ($ret as $key => $value) 
		{
			$ret[$key]['com_tel'] = $this->db->select('u_tel')
											 ->where(array('u_id' => $value['u_id']))
											 ->get('users_1')
											 ->result_array()[0]['u_tel'];

			$ret[$key]['com_nickname'] = null;
			$ret[$key]['is_like'] = 0;
			if ( $nick = $this->db->select('u_nickname, u_imgpath')
						 		  ->where(array('u_id' => $value['u_id']))
						 		  ->get('users_2')
						          ->result_array())
			{
				$ret[$key]['com_nickname'] = $nick[0]['u_nickname'];
				$ret[$key]['com_imgpath'] = $nick[0]['u_imgpath'];
			}
			if ($value['u_id'] == $u_id)
			{
				$ret[$key]['is_like'] = 1;
			}
			filter($ret[$key], $members);
		}
		return $ret;
	}


	/*
	 * 关注卖家
	 */
	public function fan($form)
	{
		//check token & get user
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
		}

		if ( ! $ret = $this->db->select('u_id, u_isseller')
							   ->where(array('u_tel' => $form['u_tel']))
							   ->get('users_1')
							   ->result_array())
		{
			throw new Exception("invalid seller_tel", 406);
		}
		$ret=$ret[0];
		if (!$ret['u_isseller'])
		{
			throw new Exception("invalid seller_tel", 406);
		}

		//update
		$que=array(
			'fan_from' => $u_id,
			'fan_to' =>$ret['u_id']
		);
		if ( $this->db->select()
					  ->where($que)
					  ->get('fans')
					  ->result_array())
		{
			throw new Exception("repeat attention", 406);
		}
		$where = array('u_id' => $ret['u_id']);
		$fans = $this->db->select('u_fans')
						 ->where($where)
						 ->get('users_3')
						 ->result_array()[0]['u_fans'];
		$data = array('u_fans' => $fans+1);
		$this->db->update('users_3', $data, $where);
		$this->db->insert('fans', $que);
	}


	/*
	 * 关注专业
	 */
	public function fanmajor($form)
	{
		//check token & get user
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
		}

		//check if follow
		$wheres = array(
			'sys_mid' => $form['sys_mid'],
			'u_id' => $u_id
		);
		if ($this->db->select()
					 ->where($wheres)
					 ->get('major_follows')
					 ->result_array())
		{
			throw new Exception("repeat attention", 406);
		}

		$where = array('sys_mid' => $form['sys_mid']);
		if (! $follow = $this->db->select('sys_mfollows')
						 	 	 ->where($where)
					 			 ->get('sys_major')
					 			 ->result_array())
		{
			throw new Exception("invalid sys_mid", 406);
		}
		$data = array('sys_mfollows' => $follow[0]['sys_mfollows']+1);
		$this->db->update('sys_major', $data, $where);
		$this->db->insert('major_follows', $wheres);
	}


	/*
	 * 获取专业列表
	 */
	public function get_majorlist($form)
	{
		//check token
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$this->my_user->check_token($form['token']);
		}

		//get list
		$ret = $this->db->select()
						->get('sys_major')
						->result_array();

		return $ret;
	}


	/*
	 * 获取关注的专业列表
	 */
	public function get_fanmajorlist($form)
	{
		//check token && get user
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
		}

		if (!$ret = $this->db->select('sys_mid')
							 ->where(array('u_id' => $u_id))
							 ->get('major_follows')
							 ->result_array())
		{
			throw new Exception("no attention major", 406);
		}
		foreach ($ret as $key => $value) {
			$ans[$key] = $this->db->select()
								  ->where($value)
								  ->get('sys_major')
								  ->result_array()[0];
		}
		return $ans;
	}


	/*
	 * 获取卖家列表
	 */
	public function get_sellerlist($form)
	{
		//check token
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$id = $this->my_user->get($form);
		}

		$data = array('u_tel', 'u_nickname', 'u_imgpath', 'u_credit', 'u_level', 'u_intro',
					  'u_fans', 'u_coulen', 'u_cousales', 'u_cousum', 'u_coucsr');

		$where=array('u_isseller=' => 0);
		$ret = $this->db->select($data)
						->join('users_2','users_2.u_id=users_1.u_id')
						->join('users_3','users_3.u_id=users_1.u_id')
						->get_where('users_1', $where)
						->result_array();

		foreach ($ret as $key => $value) {
			$ret[$key]['is_follow'] = 0;
			$where = array(
				'fan_to' => $this->db->select('u_id')->where(array('u_tel'=>$value['u_tel']))
									->get('users_1')->result_array()[0]['u_id'],
				'fan_from' => $id
			);
			if ($que = $this->db->select()->where($where)->get('fans')->result_array())
			{
				$ret[$key]['is_follow'] = 1;
			}
		}

		return $ret;
	}


	/* 
	 * 获取关注卖家列表
	 */
	public function get_fansellerlist($form)
	{
		//check token && get user
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
		}

		if (!$ret = $this->db->select('fan_to')
							 ->where(array('fan_from' => $u_id))
							 ->get('fans')
							 ->result_array())
		{
			throw new Exception("no attention seller", 406);
		}

		$data = array('u_tel', 'u_nickname', 'u_imgpath', 'u_credit', 'u_level', 'u_intro',
					  'u_fans', 'u_coulen', 'u_cousales', 'u_cousum', 'u_coucsr', 'u_sch');
		foreach ($ret as $key => $value) {
			$ans[$key] = $this->db->select($data)
								->join('users_2','users_2.u_id=users_1.u_id')
								->join('users_3','users_3.u_id=users_1.u_id')
								->get_where('users_1', array('users_1.u_id' => $value['fan_to']))
								->result_array()[0];
		}
		return $ans;
	}
	/**
	*买家确认授课完成-转入待评价
	*/
	public function wait_evaluate($form)
	{
		//check token
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$where = array('order_id' => $form['order_id']);
		//确认订单存在
		$ret = $this->db->select('order_state,c_id')
				->where($where)
				->get('orders')
				->row_array();
		
		if(empty($ret))
		{
			throw new Exception("订单不存在", 406);	
		}

		$w = array('c_id' => $ret['c_id']);
 		
		$r = $this->db->select('c_state')
					->where($w)
					->get('courses_2')
					->row_array();
		if(empty($ret))
		{
			throw new Exception("课程不存在", 406);	
		}

		//do update
		$order_state = array('order_state' => 3);
		$c_state = array('c_state' => 3);
		if( $r['c_state'] == 2 && $ret['order_state'] == 2)
		{
			$this->db->set($order_state)
					->where($where)
					->update('orders');
			
			$this->db->set($c_state)
					->where($w)
					->update('courses_2');
		}else
		{
			$data = array('c_state' => $r['c_state'],
						'order_state' => $ret['order_state']);

			throw new Exception("wrong state", 406);

			return $data;
			
		}
	}


	/*
	 * 搜索卖家
	 */
	public function get_seller($form)
	{
		//check token
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$data = array('u_tel', 'u_nickname', 'u_imgpath', 'u_credit', 'u_level', 'u_intro',
					  'u_fans', 'u_coulen', 'u_cousales', 'u_cousum', 'u_coucsr');

		$where = array('u_tel' => $form['key']);
		$ret = $this->db->select($data)
						->like('u_tel',$form['key'])
						->or_like('u_nickname', $form['key'])
						->join('users_2','users_2.u_id=users_1.u_id')
						->join('users_3','users_3.u_id=users_1.u_id')
						->get_where('users_1', array('u_isseller=' => 1))
						->result_array();

		return $ret;
	}


	/*
	 * 模糊搜索课程
	 */
	public function get_cou($form)
	{
		//check token
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$data = array('courses_1.c_id', 'c_name', 'c_star', 'c_time', 'c_place', 'c_price', 'c_imgpath');

		$where = array('c_state>='=>1,'c_state<='=>3);
		$ret = $this->db->select($data)
						->like(array('c_name' => $form['key']))
						->join('courses_1', 'courses_2.c_id=courses_1.c_id')
						->get_where('courses_2',$where)
						->result_array();

		return $ret;
	}
}

?>