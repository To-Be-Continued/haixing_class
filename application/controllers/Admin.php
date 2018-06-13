<?php
//允许网页跨域访问
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Utoken");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{
	/*********************************************************************************
	*******************
	*测试区
	*********************************************************************************
	******************/
	public function test()
	{
		$post = array(
   			'v_id' => $this->input->post('v_id')
   		);
		echo json_encode($post);
	}

	/********************************************************************************
	*******************
	*工具集
	*******************************************************************************
	******************
	/******************************************************************************
	********************
	*主接口
	*******************************************************************************
	******************/

	/**
	*login
	*/
	public function login()
	{
		$this->load->view('login/login.html');
	}

	/**
	*home
	*/
	public function home()
	{
		$this->load->view('home/home.html');
	}
	
	/**
	*enter home
	*/
	public function  enter_home()
	{
		//config
		$members = array('u_tel','u_pwd');
		
		try
		{
			//get post
			$post = get_post();
			if(empty($post))
			{
				$post = array(
					'u_tel' => $this->input->post('u_tel'),
					'u_pwd' => $this->input->post('u_pwd')
				);
			}

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if( ! $this->form_validation->run('login'))
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
			$this->load->model('User_model','my_user');
			$data = $this->my_user->login(filter($post,$members));

			//store token into session
			$this->session->set_userdata($data); 

		}catch(Exception $e)
		{
			$message = $e->getMessage();
			echo "<script type=\"text/javascript\">alert(\"$message\");</script>";
			redirect('Admin/login');
		}
		//return
		redirect('Admin/home');
	}

	//show top
	public function top()
	{
		$this->load->view('home/top.html');
	}

	//show left
	public function left()
	{
		$this->load->view('home/left.html');
	}

	//show main
	public function main()
	{
		$this->load->view('home/main.html');
	}

	/**
	*get table data
	*/
	public function cou_list()
	{
		//config
		$member = array('token');

		try
		{
			//get token
			$post['token'] = $this->input->post('token');
			//get data
			$this->load->model('Admin_model','my_admin');
			$ret = $this->my_admin->cou_list(filter($post,$member));

		}catch(Exception $e)
		{
			output_data($e->getCode(),$e->getMessage(),array());
			return;
		}

		//return
		output_data(400, '获取成功',$ret);	
	}
	/**
	*cou_examine
	*/
	public function cou_examine()
	{
		//config
		$members = array ('token','c_id');

		try{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post['c_id'] = $this->input->post('c_id');
				$post['token'] = $this->input->post('token');
			}
			//$post['token'] = get_token();


			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if( ! $this->form_validation->run('cou_examine'))
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

			//filter && delete
			$this->load->model('Admin_model','my_admin');
			$this->my_admin->cou_examine(filter($post, $members));

		}catch(Exception $e)
		{
			output_data($e->getCode(),$e->getMessage(),array());
			return;
		}

		//return
		output_data(400, '审核通过', array());
	}
	/**
	*图片轮播
	*/
	public function picture_rotation()
	{
		//config
		$member = array ('token');

		try{
			//get token
			$post['token'] = get_token();

			//filter $$ list
			$this->load->model('Admin_model','my_admin');
			$data = $this->my_admin->picture_rotation(filter($post,$member));

		}catch(Exception $e)
		{
			output_data($e->getCode(),$e->getMessage(),array());
			return;
		}

		//return
		output_data(400,'获取成功',$data);
	}
}
?>