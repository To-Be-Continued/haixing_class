<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

	/*****************************************************************************************************
	 * 私有工具集
	 *****************************************************************************************************/


	/**
	 * 生成一个未被占用的Utoken
	 */
	private function create_token()
	{
		$this->load->helper('string');
		$token=random_string('alnum',30);
		while ($this->db->where(array('token'=>$token))
			->get('sys_token')
			->result_array());
		{
			$token=random_string('alnum',30);
		}
		return $token;
	}


	/**
	 * 检测时间差
	 */
	private function is_timeout($last_visit)
	{
		$this->load->helper('date');
		$pre_unix = human_to_unix($last_visit);
		$now_unix = time();
		return $now_unix - $pre_unix < 0;
	}


	/**********************************************************************************************
	 * 公开工具集
	 **********************************************************************************************/


	/**
	 * 检测凭据
	 */
	public function check_token($token)
	{
		$where = array('token' => $token);
		if ( ! $result = $this->db->select('last_visit')
			->where(array('token' => $token))
			->get('sys_token')
			->result_array())
		{
			throw new Exception('会话已过期，请重新登陆', 401);
		}
		else
		{
			$user = $result[0];
			if ($this->is_timeout($user['last_visit']))
			{
				throw new Exception('会话已过期，请重新登陆', 401);
			}
			else
			{
				//刷新访问时间
				$new_data = array('last_visit' => date('Y-m-d H:i:s',time()));
				$this->db->update('sys_token', $new_data, $where);
			}
		}
	}


	/*
	 * 获取用户
	 */
	public function get($form)
	{
		//check token & get user
		if (isset($form['token']))
		{
			$this->check_token($form['token']);
		}
		$where = array('token' => $form['token']);
		$user = $this->db->select('u_id')
					 ->where($where)
					 ->get('sys_token')
					 ->result_array()[0]['u_id'];

		return $user;
	}


	/**********************************************************************************************
	 * 业务接口
	 **********************************************************************************************/


	/**
	 * 注册
	 */
	public function register($form)
	{
		//config
		$members = array('u_tel', 'u_pwd');
		$members_token = array('token', 'last_visit', 'u_id');
		$members_info = array('u_id', 'u_imgpath');

		//check u_tel
		$where = array('u_tel' => $form['u_tel']);
		if ( $result = $this->db->select('u_tel')
			->where($where)
			->get('users_1')
			->result_array())
		{
			throw new Exception('该手机号已注册', 403);
		}

		//DO register
		$form['u_pwd']=md5($form['u_pwd']);
		$this->db->insert('users_1',filter($form,$members));
		$result['u_id'] = $this->db->insert_id();
		$result['token'] = $this->create_token();
		$this->db->insert('sys_token',filter($result,$members_token));

		//set user_img
		$result['u_imgpath'] = base_url() . 'uploads/user_img/user.jpg';
		$this->db->insert('users_2', filter($result,$members_info));
	}


	/*
	 * login
	 */
	public function login($form)
	{
		//check u_tel
		$form['u_pwd'] = md5($form['u_pwd']);
		if ( ! $result = $this->db->select('u_id')
							  ->where($form)
				   			  ->get('users_1')
							  ->result_array())
		{
			throw new Exception('密码错误或手机号错误', 405);
		}

		//update token
		$where = array('u_id' => $result[0]['u_id']);
		$user = $this->db->select('last_visit')
						 ->where($where)
						 ->get('sys_token')
						 ->result_array()[0];
		$new_data = array('last_visit' => date('Y-m-d H:i:s', time()));
		if($this->is_timeout($user['last_visit']))
		{
			$new_data['token'] = $this->create_token();
		}
		$this->db->update('sys_token',$new_data, $where);

		//return ret
		$ret = array(
			'token' => $this->db->select('token')
						   ->where($where)
						   ->get('sys_token')
						   ->result_array()[0]['token']);
		return $ret;
	}


	/*
	 * 上传头像
	 */
	public function upload_img($form)
	{
		//config
		$member = array('u_imgpath');

		//check token
		if (isset($form['token']))
		{
			$this->check_token($form['token']);
		}

		//select user
		$where = array('u_id' => $form['u_id']);
		$data = filter($form, $member);
		$this->db->update('users_2', $data, $where);

		return $data;
	}
}
?>