<?php
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
}
?>