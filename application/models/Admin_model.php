<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model{

	/*****************************************************************************************************
	 * 私有工具集
	 *****************************************************************************************************/


	/**********************************************************************************************
	 * 公开工具集
	 **********************************************************************************************/


	/**********************************************************************************************
	 * 业务接口
	 **********************************************************************************************/
	public function cou_list($form)
	{
		//check token $$ get u_id
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$data = array('courses_1.c_id','c_imgpath','c_name','c_major','c_detail'
						,'c_time','c_place','c_price','c_releaseid');
		$where = array('c_state' => 0);
		$ret = $this->db->limit(4,0)
					->select($data)
					->join('courses_2','courses_2.c_id=courses_1.c_id')
					->get_where('courses_1',$where)
					->result_array();

		if ( empty($ret) )
		{
			throw new Exception("无待审核课程", 406);
		}

		return $ret;
	}
	/**
	*审核课程
	*/
	public function cou_examine($form)
	{
		if (isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$where = array('c_id' =>$form['c_id']);
		//check
		if (! $this->db->select('c_id')
					   ->where($where)
					   ->get('courses_1')
					   ->result_array())
		{
			throw new Exception('数据库错误',406);
		}

		$where = array('c_id' => $form['c_id']);
		//alter 课程状态
		$this->db->set('c_state',1)
				->where($where)
				->update('courses_2');
	}
	/**
	*图片轮播
	*/
	public function picture_rotation($form)
	{
		//check token && get user
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		if( ! $ret = $this->db->select('pic_id,pic_path')
								->get('picturerotation')
								->result_array())
		{
			throw new Exception("no pictures", 406);
		}

		return $ret;

	}

	/**
	*审核用户列表
	*/
	public function users_list()
	{
		//check token
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		$data = array('u_id','u_iden','u_name','u_sex','u_sch','u_major');

		$where = array( 'u_isseller' => 1);
		if(!$ret=$this->db->limit(4,0)
							->select($data)
							->get_where('users_1',$where)
							->result_array())
		{
			throw new Exception('无待认证用户',406);
		}

		//return 
		return $ret;
	}

}
?>