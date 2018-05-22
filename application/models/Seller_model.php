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
		$members = array('c_name', 'c_major', 'c_detail', 'c_imgpath', 'c_releaseid');
		$members_info = array('c_id', 'c_price', 'c_time', 'c_place');

		//check token && get u_id
		if (isset($form['token'])) 
		{
			$this->load->model('User_model', 'my_user');
			$u_id = $this->my_user->get($form);
		}

		if(!isset($form['tags']))
		{
			throw new Exception("标签字段不能为空", 1);
			
		}

		//object translate into json array
		$arr = json_decode(json_encode($form['tags']),true);
		
		if(empty($arr))
		{
			throw new Exception("至少含有一个标签");
			
		}
		//check if exist
		$arr = array('c_major', 'c_name', 'c_detail');
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
		//$form['c_imgpath'] = base_url() . 'uploads/class_img/class.jpg';
		$this->db->insert('courses_1', filter($form, $members));
		$cid=$this->db->insert_id();
		$form['c_id'] = $cid;
		$this->db->insert('courses_2', filter($form, $members_info));

		foreach ($arr as $key => $value) {
			$where = array(
				'tag_text' => $value,
				'c_id'     => $form['c_id']
			);
			$this->db->insert('tags',$where);			
		}
	}


	/*
	 * 卖家界面_审核中课程列表
	 */
	public function cou_check($form) 
	{
		//check token && get u_id
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$u_id = $this->my_user->get($form);
		}

		//get info
		$where = array('c_releaseid' => $u_id, 'c_state' => 0);
		$ret = $this->db->select('c_name,c_imgpath,c_price,c_time,c_state,courses_1.c_id')
						->join('courses_2', 'courses_1.c_id=courses_2.c_id')
						->get_where('courses_1', $where)
						->result_array();

		if (empty($ret))
		{
			throw new Exception("无待审核课程", 406);
		}

		return $ret;
	}


	/*
	 * 卖家界面_上架中课程列表
	 */
	public function cou_grounding($form)
	{
		//check token && get u_id
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$u_id = $this->my_user->get($form);
		}

		//get info
		$where = array('c_releaseid' => $u_id, 'c_state >=' => 1, 'c_state <=' => 1);
		$ret = $this->db->select('c_name,c_imgpath,c_price,c_time,c_state,courses_1.c_id')
						->join('courses_2', 'courses_1.c_id=courses_2.c_id')
						->get_where('courses_1', $where)
						->result_array();

		if (empty($ret))
		{
			throw new Exception("无上架课程", 406);
		}

		return $ret;
	}


	/*
	 * 卖家界面_已下架课程列表
	 */
	public function cou_undercarriage($form)
	{
		//check token && get u_id
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$u_id = $this->my_user->get($form);
		}

		//get info
		$where = array('c_releaseid' => $u_id, 'c_state' => 3);
		$ret = $this->db->select('c_name,c_imgpath,c_price,c_time,c_state,courses_1.c_id')
						->join('courses_2', 'courses_1.c_id=courses_2.c_id')
						->get_where('courses_1', $where)
						->result_array();

		if (empty($ret))
		{
			throw new Exception("无下架课程", 406);
		}

		return $ret;
	}


	/*
	 * 上传课程头像
	 */
	public function upload_img($form)
	{
		//config
		$member = array('c_imgpath');

		//check token
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$this->my_user->check_token($form['token']);
		}

		//check c_id
		$where = array('c_id' => $form['c_id']);
		if ( ! $ret = $this->db->select('c_id')
							   ->where($where)
							   ->get('courses_1')
							   ->result_array())
		{
			throw new Exception("课程不存在");
		}

		//update
		$data = filter($form, $member);
		$this->db->update('courses_1', $data, $where);

		return $data;
	}


	/**
	*卖家端点击进入课程详情
	*/
	public function cou_entercou($form)
	{
		//check && get u_id
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$u_id = $this->my_user->get($form);
		}
		$where = array('courses_1.c_id' => $form['c_id']);
		$data = array('c_imgpath','c_name','c_major','c_time','c_price',
					'c_intro','c_detail','c_star','c_purchase');
		$ret['c_info'] = $this->db->select($data)
				->join('courses_2','courses_1.c_id=courses_2.c_id')
				//->join('tags','courses_1.c_id=tags.c_id')
				->get_where('courses_1',$where)
				->result_array();
		//这门课程总收益
		if ( empty($ret) )
		{
			throw new Exception("invalid c_id", 406);
		}
		$ret['c_info'][0]['c_totalmoney'] = $ret['c_info'][0]['c_purchase'] *
		$ret['c_info'][0]['c_price'];
		//所有课程销量情况
		$w = array('users_1.u_id' => $u_id);
		$ret['u_info'] = $this->db->select('u_sex,u_birth,u_major,u_coucsr,u_cousum,u_cousales')
									->join('users_3','users_1.u_id=users_3.u_id')
									->get_where('users_1',$w)
									->result_array();

		return $ret;
	}


	/**
	*卖家界面-删除课程  已购买状态下不能删除
	*/
	public function cou_del($form)
	{
		//check token
		if(isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$this->my_user->check_token($form['token']);
		}

		//确认 存在课程
		$where = array('c_id' => $form['c_id']);
		$ret = $this->db->select('c_id,c_state')
				->where($where)
				->get('courses_2')
				->result_array();

		if(empty($ret))
		{
			throw new Exception("课程不存在", 406);
		}
		
		switch ($ret[0]['c_state'])
		{
			case 2:
				throw new Exception("已购买，无法删除", 406);
				break;
			case 5:
				throw new Exception("课程已删除", 406);
				break;
			default:
				//do update
				$data = array(
					'c_state' => 5
				);
				$this->db->set($data)
				->where($where)
				->update('courses_2');
				break;
		}
	}


	/**
	*卖家确认授课
	*/
	public function cou_accept($form)
	{
		// check token
		if(isset($form['token']))
		{
			$this->load->model('User_model','my_user');
			$this->my_user->check_token($form['token']);
		}

		//确认订单存在 && get c_id、c_state
		$where = array('order_id' => $form['order_id']);
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
		$data = array('order_state' => 2);
		if( $r['c_state'] == 2 && $ret['order_state'] == 1)
		{
			$this->db->set($data)
					->where($where)
					->update('orders');
		}
	}


	/*
	 * edit courses
	 */
	public function cou_edit($form)
	{
		//config
		$members = array('c_major', 'c_detail');
		$members_info = array('c_price', 'c_time', 'c_place', 'c_state');

		//check token && get u_id
		if (isset($form['token'])) 
		{
			$this->load->model('User_model', 'my_user');
			$u_id = $this->my_user->get($form);
		}

		if(!isset($form['tags']))
		{
			throw new Exception("标签字段不能为空", 1);
			
		}

		//object translate into json array
		$arr = json_decode(json_encode($form['tags']),true);
		
		if(empty($arr))
		{
			throw new Exception("至少含有一个标签");
			
		}

		//check if exist
		$where = array('c_id' => $form['c_id']);
		if ( ! $ret =  $this->db->select()
							  	->where($where)
					  			->get('courses_1')
					  			->result_array())
		{
			throw new Exception('该课程不存在', 406);
		}

		if ( $ret[0]['c_releaseid'] != $u_id)
		{
			throw new Exception('无权限更改');
		}

		$state = $this->db->select('c_state')
						  ->where($where)
						  ->get('courses_2')
						  ->result_array()[0]['c_state'];
		
		if ($state > 1)
		{
			throw new Exception("课程无法修改", 406);
		}
		//do update
		$form['c_state'] = 0;
		$this->db->update('courses_1', filter($form, $members), $where);
		$this->db->update('courses_2', filter($form, $members_info), $where);

		$this->db->delete('tags', $where);
		foreach ($arr as $key => $value) {
			$where = array(
				'tag_text' => $value,
				'c_id'     => $form['c_id']
			);
			$this->db->insert('tags',$where);			
		}
	}


	/*
	 * 卖家拒接授课或者买家拒接
	 */
	public function cou_deny($form)
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
		
		if ($state == 2 && $ret[0]['order_state'] == 1)
		{
			$data = array('order_state' => 5);
			$where = array('order_id' => $form['order_id']);
			$this->db->update('orders', $data, $where);
		}
		else
		{
			throw new Exception("invalid order_state or c_state", 406);	
		}
	}
}

?>