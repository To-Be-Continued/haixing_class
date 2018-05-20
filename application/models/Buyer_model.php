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
				 	  'orders.c_id', 'c_num', 'c_imgpath','c_price');
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
}

?>