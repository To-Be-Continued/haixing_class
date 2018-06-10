<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once "wxBizDataCrypt.php";

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


	/*
	 * 获取openid
	 */
	private function getopenid($code)
	{
		$appid = 'wx196440f4d0464441';
		$secret = '9fe0991c34a49ac73d4a73ba1d7d4b40';
		//$appid = 'wxc84bc967d806aa31';
		//$secret = 'fa5299e32a1bae1024d37ae69903553c';
		$json = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
		header("Content-Type: application/json");
		$js =  file_get_contents($json);
		$data = json_decode($js, true);
		return $data;
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
		$members_3 = array('u_id');

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
		$this->db->insert('users_2', filter($result, $members_info));
		$this->db->insert('users_3', filter($result, $members_3));
		$this->db->insert('users_setting',filter($result,$members_3));
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


	/*
	 * 微信注册
	 */
	public function WeChatregister($form)
	{
		//config
		$members = array('u_tel', 'u_pwd', 'openid');
		$members_token = array('token', 'last_visit', 'u_id');
		$members_info = array('u_id', 'u_imgpath');
		$members_3 = array('u_id');

		//check u_tel
		$where = array('u_tel' => $form['u_tel']);
		if ( $result = $this->db->select('u_tel')
			->where($where)
			->get('users_1')
			->result_array())
		{
			throw new Exception('该手机号已注册', 403);
		}

		//get openid
		$res = $this->getopenid($form['code']);
		if (!isset($res['openid']))
		{
			throw new Exception("invalid code");
		}
		$form['openid'] = $res['openid'];

		//DO register
		$form['u_pwd']=md5($form['u_pwd']);
		$this->db->insert('users_1',filter($form,$members));
		$result['u_id'] = $this->db->insert_id();
		$result['token'] = $this->create_token();
		$this->db->insert('sys_token',filter($result,$members_token));

		//set user_img
		$result['u_imgpath'] = base_url() . 'uploads/user_img/user.jpg';
		$this->db->insert('users_2', filter($result,$members_info));
		$this->db->insert('users_3', filter($result, $members_3));
		$this->db->insert('users_setting',filter($result,$members_3));
	}


	/*
	 * 微信登录
	 */
	public function WeChatlogin($form)
	{
		//config
		$members = array('token', 'openid', 'session_key');

		$data = $this->getopenid($form['code']);
		if (! isset($data['openid']))
		{
			throw new Exception("invalid code");
		}

		//check open_id
		$wheres = array('openid' => $data['openid']);
		if ( ! $result = $this->db->select('u_id')
							  ->where($wheres)
				   			  ->get('users_1')
							  ->result_array())
		{
			throw new Exception('账号不存在', 406);
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
		$data['token'] = $this->db->select('token')
						 		  ->where($where)
						   		  ->get('sys_token')
								  ->result_array()[0]['token'];
		return $data;
	}


	/*
	 * 获取phone number
	 */
	public function get_tel($form)
	{
		$data = $this->getopenid($form['code']);
		if (! isset($data['openid']))
		{
			throw new Exception("invalid code");
		}

		$pc = new wxBizDataCrypt('wx196440f4d0464441', $data['session_key']);
		$errCode = $pc->decryptData($form['encryptedData'], $form['iv'], $data);
		if ($errCode)
		{
			throw new Exception('获取失败');
		}
		else
			return json_decode($data, true);
	}


	/*
	 * 获取用户信息
	 */
	public function get_info($form)
	{
		//check token & get user
		if (isset($form['token']))
		{
			$this->check_token($form['token']);
		}

		//get infomation
		$data = array('u_nickname', 'u_sex', 'u_birth', 'u_isseller', 'u_isiden', 'u_email',
					  'u_qq', 'u_intro', 'u_imgpath', 'u_sch', 'u_major', 'u_name');
		if (! $ret = $this->db->select($data)
						->join('users_2', 'users_1.u_id=users_2.u_id')
						->get_where('users_1', array('u_tel' => $form['u_tel']))
						->result_array())
		{
			throw new Exception("invalid u_tel", 406);
		}
		return $ret[0];
	}


	/*
	 * 用户设置
	 */
	public function user_setting($form)
	{
		$members = array('f_age', 'f_sch', 'f_name', 'f_major');
		//check token & get user
		if (isset($form['token']))
		{
			$id = $this->get($form);
		}
		$where = array('u_id' => $id);
		$this->db->update('users_setting', filter($form, $members), $where);
	}


	/*
	 * 获取用户设置
	 */
	public function get_setting($form)
	{
		//check token & get user
		if (isset($form['token']))
		{
			$id = $this->get($form);
		}

		$data = array('f_age', 'f_sch', 'f_name', 'f_major');
		$where = array('u_id' => $id);
		if (! $ret = $this->db->select($data)
							  ->where($where)
							  ->get('users_setting')
							  ->result_array())
		{
			throw new Exception("System Error", 406);
		}

		return $ret[0];
	}


	/*
	 * 修改用户信息
	 */
	public function update_info($form)
	{
		//config
		$members_1 = array('u_email', 'u_qq', 'u_name', 'u_sex', 'u_birth', 'u_sch', 'u_major');
		$members_2 = array('u_nickname', 'u_intro');

		//check token & get user
		if (isset($form['token']))
		{
			$id = $this->get($form);
		}

		$where = array('u_id' => $id);

		//do update
		$this->db->update('users_1', filter($form, $members_1), $where);
		$this->db->update('users_2', filter($form, $members_2), $where);
	}


	/*
	 * 设置昵称
	 */
	public function set_nickname($form)
	{
		$members = array('u_nickname');
		//check token & get user
		if (isset($form['token']))
		{
			$id = $this->get($form);
		}

		if ($this->db->select('u_nickname')
					 ->where(array('u_nickname' => $form['u_nickname']))
					 ->get('users_2')
					 ->result_array())
		{
			throw new Exception("invalid nickname", 406);
		}

		$where = array('u_id' => $id);
		$this->db->update('users_2', filter($form, $members), $where);
	}	
	/**
	*用户点赞
	*/
	public function give_thumbup($form)
	{
		//check token && get u_id
		if (isset($form['token']))
		{
			$this->load->model('User_model', 'my_user');
			$u_id = $this->my_user->get($form);
		}

		$data = array('u_id' => $u_id,'com_id' => $form['com_id']);
		
		if($ret = $this->db->select('com_id')
						->get_where('comments',array('com_id' => $form['com_id']))
						->row_array())
		{
			$this->db->insert('thumbsup',$data);
		}else
		{
			throw new Exception("comment not exist", 406);
		}
		
	}


	/**
	*储存微信头像
	*/
	public function storage_imgpath($form)
	{
		//config
		$member = array('u_imgpath');
		//check token & get user
		if (isset($form['token']))
		{
			$id = $this->get($form);
		}

		$where = array('u_id' => $id);

		//do update
		$this->db->update('users_2', filter($form, $member), $where);
	}
}
?>