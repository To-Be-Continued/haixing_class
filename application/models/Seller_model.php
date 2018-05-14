<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Seller_model extends CI_Model
{
	/*****************************************************************************************************
	 * 私有工具集
	 *****************************************************************************************************/


	/**********************************************************************************************
	 * 公开工具集
	 **********************************************************************************************/


	/**********************************************************************************************
	 * 业务接口
	 **********************************************************************************************/


	/*
	 * create course
	 */
	public function cou_release($form)
	{
		//config
		$members = array('c_name', 'c_intro', 'c_major', 'c_detail', 'c_imgpath', 'c_releaseid');
		$members_info = array('c_id', 'c_price', 'c_len', 'c_time', 'c_place');


		//check token && get u_id
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$u_id = $this->my_user->get($form);
		}

		//check if exist
		$arr = array('c_intro', 'c_major', 'c_name', 'c_detail');
		$data = filter($form, $arr);
		if ( $this->db->select()
					  ->like($data)
					  ->get('courses_1')
					  ->result_array())
		{
			throw new Exception('已发布该课程', 402);
		}
		
		//do insert
		$form['c_releaseid'] = $u_id;
		$form['c_imgpath'] = base_url() . 'uploads/class_img/class.jpg';
		$this->db->insert('courses_1', filter($form, $members));
		$cid=$this->db->insert_id();
		$form['c_id'] = $cid;
		$this->db->insert('courses_2', filter($form, $members_info));
	}


	/*
	 * 卖家界面_审核中课程列表
	 */
	public function cou_order($form)
	{
		//check token
		//check token && get u_id
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$u_id = $this->my_user->get($form);
		}

		//get info
		$where = array('c_releaseid' => $u_id, 'c_state' => 0);
		$ret = $this->db->select()
						->join('courses_2', 'courses_1.c_id=courses_2.c_id')
						->get_where('courses_1', $where)
						->result_array();

		if (empty($ret))
		{
			throw new Exception("无待审核课程", 406);
		}

		return $ret;
	}

}

?>