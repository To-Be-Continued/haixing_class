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
}
?>