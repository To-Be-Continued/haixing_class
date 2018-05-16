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
}

?>