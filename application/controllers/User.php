<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class User extends CI_Controller {


	/*****************************************************************************************************
	 * 测试区域
	 *****************************************************************************************************/
	public function test()
	{
	}


	/*****************************************************************************************************
	 * 工具集
	 *****************************************************************************************************/


	/*****************************************************************************************************
	 * 主接口
	 *****************************************************************************************************/


	/**
	 * 注册
	 */
	public function register()
	{
		//config
		$members = array('u_tel', 'u_pwd');

		//register
		try
		{
			//get post
			$post = get_post();
			if ( empty($post) )
			{
				$post = array(
					'u_tel'  => $this->input->post('u_tel') ,
					'u_pwd'  => $this->input->post('u_pwd')
				);
			}

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if ( ! $this->form_validation->run('register'))
			{
				$this->load->helper('form');
				foreach ($members as $member)
				{
					if (form_error($member))
					{
						throw new Exception(strip_tags(form_error($member)));
					}
				}
				return;
			}

			//过滤 && register
			$this->load->model('User_model','my_user');
			$this->my_user->register(filter($post, $members));

		}
		catch (Exception $e)
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '注册成功', array());
	}


	/*
	 * login
	 */
	public function login()
	{
		//config
		$members = array('u_tel', 'u_pwd');

		try
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post = array(
					'u_tel' => $this->input->post('u_tel'),
					'u_pwd' => $this->input->post('u_pwd')
				);
			}

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if ( ! $this->form_validation->run('login'))
			{
				$this->load->helper('form');
				foreach ($members as $member)
				{
					if (form_error($member))
					{
						throw new Exception(strip_tags(form_error($member)));
					}
				}
				return;
			}

			//filter && login
			$this->load->model('User_model', 'my_user');
			$data = $this->my_user->login(filter($post, $members));

		}
		catch (Exception $e)
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;	
		}
		
		//return
		output_data(400, '登录成功', $data);
	}


	/*
	 * 上传头像
	 */
	public function upload_img()
	{
		if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") 
		{
			return;
		}

		//config
		$members = array('token', 'u_id', 'u_imgpath');

		//get u_tel
		$post['token'] = get_token();
		$this->load->model('User_model', 'my_user');
		$user = $this->my_user->get($post);
		$post['u_id'] = $user;

		//upload config
		$config['upload_path'] = './uploads/user_img/';
		$config['allowed_types'] = 'jpg|png';
		$config['file_name'] = $user;
		$config['overwrite'] = TRUE;
		$config['max_size'] = 10000;
		$config['max_width'] = 1980;
		$config['max_height'] = 1024;

		//upload
		try
		{
			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('userfile'))
        	{
            	throw new Exception($this->upload->display_errors());
	        }
    		else
        	{
        		$data = array('upload_data' => $this->upload->data()); 
            	$post['u_imgpath'] = base_url().'uploads/user_img/'.$data['upload_data']['file_name'];;

            	//upload & filter            	
            	$this->load->model('User_model', 'user');
            	$ret = $this->user->upload_img(filter($post, $members));
            	
        	}
		}
		catch(Exception $e)
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '上传成功', $ret);
	}


	/*
	 *
	 */
	public function WeChatregister()
	{
		//config
		$members = array('code', 'u_tel', 'u_pwd');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post = array(
					'code' => $this->input->post('code'),
					'u_tel' => $this->input->post('u_tel'),
					'u_pwd' => $this->input->post('u_pwd')
				);
			}

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);

			if ( ! $this->form_validation->run('wechatregister'))
			{
				$this->load->helper('form');
				foreach ($members as $member)
				{
					if (form_error($member))
					{
						throw new Exception(strip_tags(form_error($member)));
					}
				}
				return;
			}

			//filter && register
			$this->load->model('User_model', 'my_user');
			$this->my_user->WeChatregister(filter($post, $members));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;	
		}

		//return
		output_data(400, '注册成功', array());
	}


	/*
	 * 微信登录
	 */
	public function WeChatlogin()
	{
		//config
		$members = array('code');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post['code'] = $this->input->post('code');
			}

			$this->load->library('form_validation');
			$this->form_validation->set_rules('code', 'code', 'required');
			if ( ! $this->form_validation->run())
			{
				$this->load->helper('form');
				foreach ($members as $member)
				{
					if (form_error($member))
					{
						throw new Exception(strip_tags(form_error($member)));
					}
				}
				return;
			}

			//filter & login
			$this->load->model('User_model', 'my_user');
			$data = $this->my_user->WeChatlogin(filter($post, $members));	
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;	
		}

		output_data(400, '登录成功', $data);
	}


	/*
	 * 小程序获取手机号
	 */
	public function get_tel()
	{
		//config
		$members = array('code', 'encryptedData', 'iv');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post = array(
					'code' => $this->input->post('code'),
					'encryptedData' => $this->input->post('encryptedData'),
					'iv' => $this->input->post('iv')
				);
			}

			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if ( ! $this->form_validation->run('get_tel'))
			{
				$this->load->helper('form');
				foreach ($members as $member)
				{
					if (form_error($member))
					{
						throw new Exception(strip_tags(form_error($member)));
					}
				}
				return;
			}

			//filter & get u_tel
			$this->load->model('User_model', 'my_user');
			$data = $this->my_user->get_tel(filter($post, $members));	
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;	
		}

		output_data(400, '获取成功', $data);
	}


	/*
	 * 获取用户信息
	 */
	public function get_info()
	{
		//config
		$members = array('token', 'u_tel');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post['u_tel'] = $this->input->post('u_tel');
			}
			$post['token']=get_token();

			$this->load->library('form_validation');
			$this->form_validation->set_rules('u_tel', '手机号', 'required');
			if ( ! $this->form_validation->run())
			{
				$this->load->helper('form');
				foreach ($members as $member)
				{
					if (form_error($member))
					{
						throw new Exception(strip_tags(form_error($member)));
					}
				}
				return;
			}

			//filter & get_info
			$this->load->model('User_model', 'my_user');
			$data = $this->my_user->get_info(filter($post, $members));	
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;	
		}

		output_data(400, '获取成功', $data);
	}


	/*
	 * 用户设置
	 */
	public function user_setting()
	{
		//config
		$members = array('token', 'f_age', 'f_sch', 'f_name', 'f_major');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post = array(
					'f_age' => $this->input->post('f_age'),
					'f_sch' => $this->input->post('f_sch'),
					'f_name' => $this->input->post('f_name'),
					'f_major' => $this->input->post('f_major')
				);
			}
			$post['token'] = get_token();

			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if ( ! $this->form_validation->run('user_setting'))
			{
				$this->load->helper('form');
				foreach ($members as $member)
				{
					if (form_error($member))
					{
						throw new Exception(strip_tags(form_error($member)));
					}
				}
				return;
			}

			//filter & set
			$this->load->model('User_model', 'my_user');
			$this->my_user->user_setting(filter($post, $members));	
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;	
		}

		output_data(400, '设置成功', array());
	}

} 

?>