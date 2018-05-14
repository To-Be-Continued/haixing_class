<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buyer extends CI_Controller{

	/************************************************************
	******************
	*测试区域
	************************************************************
	**************/
	public function test()
	{
	}

	/************************************************************
	********************
	*工具集
	*************************************************************
	*****************/

	/***************************************************************
	*******************
	*主接口
	***************************************************************
	*****************/

	/*
	*take course into shoppingcart 
	*/
	public function cou_shopping()
	{
		//config
		$members = array('token','c_id','c_num');

		try{
			$post = get_post();
			if(empty($post))
			{
				$post = array(
					'c_id' => $this->input->post('c_id'),
					'c_num' => $this->input->post('c_num')
				);
			}
			$post['token'] = get_token();
			//check from 
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);


			if( ! $this->form_validation->run('cou_shopping'))
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

			//filter && take it
			$this->load->model('Buyer_model','my_buy');
			$this->my_buy->cou_shopping(filter($post,$members));

		}catch(Exception $e)
		{
			output_data($e->getCode(),$e->getMessage(),array());

			return;
		}

		//return
		output_data(400,'成功加入',array());
	}
	/**
	*get carts
	*/
	public function get_carts()
	{
		$member = array('token');

		try{
			//get token 
			$post['token'] = get_token();

			$this->load->model('Buyer_model','my_buy');
			$data = $this->my_buy->get_carts(filter($post,$member));

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