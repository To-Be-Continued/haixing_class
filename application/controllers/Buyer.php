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
	/**
	*enter the course in cart
	*/
	public function entercou()
	{
		//config
		$members = array('token','sp_id');

		try
		{
			//get post
			$post = get_post();
			if(empty($post))
			{
				$post = array(
					'sp_id' => $this->input->post('sp_id')
				);
			}
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if( ! $this->form_validation->run('cart_enter'))
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
			
			//filter && enter
			$this->load->model('Buyer_model','my_buy');
			$data = $this->my_buy->entercou(filter($post,$members));

		}catch(Exception $e)
		{
			output_data($e->getCode(),$e->getMessage(),array());
			return;
		}

		//return 
		output_data(400,'获取成功',$data);

	}


	/*
	 * move one cart
	 */
	public function move_cart()
	{
		//config
		$members = array('token', 'sp_id');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post['sp_id'] = $this->input->post('sp_id');
			}
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if( ! $this->form_validation->run('move_cart'))
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
			$this->load->model('Buyer_model','my_buy');
			$this->my_buy->move_cart(filter($post, $members));

		} 
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '删除成功', array());
	}


	/*
	 * move batch carts
	 */
	public function move_carts()
	{
		//config
		$members = array('token', 'sp_id');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post['sp_id'] = $this->input->post('sp_id');
			}
			$post['token'] = get_token();

			//filter && delete
			$this->load->model('Buyer_model','my_buy');
			$this->my_buy->move_carts(filter($post,$members));
		} 
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '删除成功', array());

	}


	/*
	 * 买家下订单
	 */
	public function cou_order()
	{
		//config
		$members = array('token', 'c_id', 'order_money');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post = array(
					'c_id' 		  => $this->input->post('c_id'),
					'order_money' => $this->input->post('order_money')
				);

			}
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if( ! $this->form_validation->run('cou_order'))
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
			$this->load->model('Buyer_model','my_buy');
			$this->my_buy->cou_order(filter($post, $members));

		} 
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '下单成功', array());
	}
}
?>