<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Seller extends CI_Controller {


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
	 * creat sell class
	 */
	public function cou_release()
	{
		//config
		$members = array('token', 'c_name', 'c_intro', 'c_major', 'c_detail', 
						 'c_img', 'c_price', 'c_len', 'c_time', 'c_place');

		try
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post = array(
					'c_name'   => $this->input->post('c_name')  ,
    				'c_intro'  => $this->input->post('c_intro') ,
				    'c_major'  => $this->input->post('c_major') ,
				    'c_detail' => $this->input->post('c_detail'),
				    'c_price'  => $this->input->post('c_price') ,
				    'c_len'    => $this->input->post('c_len')   ,
				    'c_time'   => $this->input->post('c_time')  ,
				    'c_place'  => $this->input->post('c_place')
				);
			}
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if ( ! $this->form_validation->run('cou_info'))
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

			//filter && creat
			$this->load->model('Seller_model', 'my_sell');
			$this->my_sell->cou_release(filter($post, $members));

		}
		catch (Exception $e)
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '发布成功', array());
	}


	/*
	 * 卖家界面_审核中课程列表
	 */
	public function cou_order()
	{
		$member = array('token');

		try
		{
			//get token
			$post['token'] = get_token();

			$this->load->model('Seller_model', 'my_sell');
			$data = $this->my_sell->cou_order(filter($post, $member));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data('400', '获取成功', $data);
	}
	
}

?>