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

		$ret = $this->db->select('sp_id,shopping_carts.c_id,c_num,c_name,c_price')
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
}

?>