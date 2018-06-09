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


	/*
	 * 买家订单列表
	 */
	public function cou_orderlist()
	{
		//config
		$members = array('token');

		try 
		{
			//get post
			$post['token'] = get_token();

			//filter && delete
			$this->load->model('Buyer_model','my_buy');
			$data = $this->my_buy->cou_orderlist(filter($post,$members));
		} 
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '获取成功', $data);
	}


	/*
	 * 获取课程详情
	 */
	public function get_cdetail()
	{
		//config
		$members = array('token', 'c_id');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post['c_id'] = $this->input->post('c_id');
			}
			$post['token'] = get_token();

			$this->load->library('form_validation');
			$this->form_validation->set_rules('c_id', '课程ID', 'required');
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

			//filter & get
			$this->load->model('Buyer_model', 'my_buy');
			$data = $this->my_buy->get_cdetail(filter($post, $members));	
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;	
		}

		output_data(400, '获取成功', $data);
	}


	/*
	 * 获取课程列表
	 */
	public function get_allcou()
	{
		//config
		$member = array('token');

		try
		{
			//get token
			$post['token'] = get_token();

			//filter && get list
			$this->load->model('Buyer_model', 'my_buy');
			$data = $this->my_buy->get_allcou(filter($post, $member));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data('400', '获取成功', $data);
	}

	/**
	*卖家付款
	*/
	public function cou_buy()
	{
		//config
		$members = array('token','order_id');

		try
		{
			//get post
			$post = get_post();
			if(empty($post))
			{
				$post = array(
					'order_id' => $this->input->post('order_id')
				);
			}
			//get token
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if( ! $this->form_validation->run('cou_buy'))
			{
				$this->load->helper('form');
				foreach ($members as $member)
				{
					if(form_error($member))
					{
						throw new Exception(strip_tags(form_error($member)));
					}
				}
				return;
			}

			//filter && buy
			$this->load->model('Buyer_model','my_buy');
			$this->my_buy->cou_buy(filter($post,$members));

		}catch(Exception $e)
		{
			output_data($e->getCode(),$e->getMessage(),array());
			return;
		}

		//return 
		output_data(400,"成功付款",array());
	}


	/*
	 * 未付款取消订单
	 */
	public function cou_cancelorder()
	{
		//config
		$members = array('token', 'order_id');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post['order_id'] = $this->input->post('order_id');
			}
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_rules('order_id', '订单ID', 'required');
			if (! $this->form_validation->run())
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

			//filter && cancel order
			$this->load->model('Buyer_model', 'my_buy');
			$this->my_buy->cou_cancelorder(filter($post, $members));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(),$e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '取消成功', array());
	}


	/*
	 * 删除订单
	 */
	public function cou_delorder()
	{
		//config
		$members = array('token', 'order_id');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post['order_id'] = $this->input->post('order_id');
			}
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_rules('order_id', '订单ID', 'required');
			if (! $this->form_validation->run())
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

			//filter && delete order
			$this->load->model('Buyer_model', 'my_buy');
			$this->my_buy->cou_delorder(filter($post, $members));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(),$e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '删除成功', array());
	}


	/*
	 * 发布课程评论
	 */
	public function comment()
	{
		//config
		$members = array('token', 'c_id', 'order_id', 'com_text', 'com_star');

		try
		{
			//get post
			$post = get_post();
			if(empty($post))
			{
				$post = array(
					'c_id' => $this->input->post('c_id'),
					'order_id' => $this->input->post('order_id'),
					'com_text' => $this->input->post('com_text'),
					'com_star' => $this->input->post('com_star')
				);
			}
			//get token
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if( ! $this->form_validation->run('comment'))
			{
				$this->load->helper('form');
				foreach ($members as $member)
				{
					if(form_error($member))
					{
						throw new Exception(strip_tags(form_error($member)));
					}
				}
				return;
			}

			//filter && comment
			$this->load->model('Buyer_model','my_buy');
			$this->my_buy->comment(filter($post,$members));

		}
		catch(Exception $e)
		{
			output_data($e->getCode(),$e->getMessage(),array());
			return;
		}

		//return
		output_data(400, '评价成功', array());
	}


	/*
	 * 获取课程评论列表
	 */
	public function get_list()
	{
		//config
		$members = array('token', 'c_id');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post['c_id'] = $this->input->post('c_id');
			}
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_rules('c_id', '课程ID', 'required');
			if (! $this->form_validation->run())
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

			//filter && get comment list
			$this->load->model('Buyer_model', 'my_buy');
			$data = $this->my_buy->get_list(filter($post, $members));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(),$e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '获取成功', $data);
	}


	/*
	 * 关注卖家
	 */
	public function fan()
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
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_rules('u_tel', '手机号', 'required');
			if (! $this->form_validation->run())
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

			//filter && get fan
			$this->load->model('Buyer_model', 'my_buy');
			$this->my_buy->fan(filter($post, $members));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(),$e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '关注成功', array());
	}


	/*
	 * 关注专业
	 */
	public function fanmajor()
	{
		//config
		$members = array('token', 'sys_mid');

		try 
		{
			//get post
			$post = get_post();
			if (empty($post))
			{
				$post['sys_mid'] = $this->input->post('sys_mid');
			}
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_rules('sys_mid', '专业ID', 'required');
			if (! $this->form_validation->run())
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

			//filter && fan
			$this->load->model('Buyer_model', 'my_buy');
			$this->my_buy->fanmajor(filter($post, $members));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(),$e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '关注成功', array());
	}


	/*
	 * 获取专业列表
	 */
	public function get_majorlist()
	{
		//config
		$members = array('token');

		try 
		{
			//get token
			$post['token'] = get_token();

			//filter && list
			$this->load->model('Buyer_model', 'my_buy');
			$data = $this->my_buy->get_majorlist(filter($post, $members));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(),$e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '获取成功', $data);
	}


	/*
	 * 获取关注的专业列表
	 */
	public function get_fanmajorlist()
	{
		//config
		$members = array('token');

		try 
		{
			//get token
			$post['token'] = get_token();

			//filter && list
			$this->load->model('Buyer_model', 'my_buy');
			$data = $this->my_buy->get_fanmajorlist(filter($post, $members));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(),$e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '获取成功', $data);
	}


	/* 
	 * 获取卖家列表
	 */
	public function get_sellerlist()
	{
		//config
		$members = array('token');

		try 
		{
			//get token
			$post['token'] = get_token();

			//filter && list
			$this->load->model('Buyer_model', 'my_buy');
			$data = $this->my_buy->get_sellerlist(filter($post, $members));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(),$e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '获取成功', $data);
	}


	/* 
	 * 获取关注卖家列表
	 */
	public function get_fansellerlist()
	{
		//config
		$members = array('token');

		try 
		{
			//get token
			$post['token'] = get_token();

			//filter && list
			$this->load->model('Buyer_model', 'my_buy');
			$data = $this->my_buy->get_fansellerlist(filter($post, $members));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(),$e->getMessage(), array());
			return;
		}

		//return
		output_data(400, '获取成功', $data);
	}
	
}
?>