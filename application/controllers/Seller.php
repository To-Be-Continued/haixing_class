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
	public function cou_check()
	{
		$member = array('token');

		try
		{
			//get token
			$post['token'] = get_token();

			$this->load->model('Seller_model', 'my_sell');
			$data = $this->my_sell->cou_check(filter($post, $member));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data('400', '获取成功', $data);
	}


	/*
	 * 卖家界面_上架中课程列表
	 */
	public function cou_grounding()
	{
		$member = array('token');

		try
		{
			//get token
			$post['token'] = get_token();

			$this->load->model('Seller_model', 'my_sell');
			$data = $this->my_sell->cou_grounding(filter($post, $member));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data('400', '获取成功', $data);
	}


	/*
	 * 卖家界面_已下架中课程列表
	 */
	public function cou_undercarriage()
	{
		$member = array('token');

		try
		{
			//get token
			$post['token'] = get_token();

			$this->load->model('Seller_model', 'my_sell');
			$data = $this->my_sell->cou_undercarriage(filter($post, $member));
		}
		catch (Exception $e) 
		{
			output_data($e->getCode(), $e->getMessage(), array());
			return;
		}

		//return
		output_data('400', '获取成功', $data);
	}


	/*
     * 上传课程封面
     */
    public function upload_img()
	{
		if ($_SERVER['REQUEST_METHOD'] == "OPTIONS")
		{
			return;
		}

		//config
		$members = array('token', 'c_id', 'c_imgpath');

		//get m_id
		$post['token'] = get_token();
		$post['c_id'] = $this->input->post('c_id');

		//upload config
		$config['upload_path'] = './uploads/class_img/';
		$config['allowed_types'] = 'jpg|png';
		$config['file_name'] = $post['c_id'];
		$config['overwrite'] = TRUE;
		$config['max_size'] = 10000;
		$config['max_width'] = 1980;
		$config['max_height'] = 1024;

		//upload
		try
		{
			//do upload
			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('userfile'))
        	{
            	throw new Exception($this->upload->display_errors());
	        }
    		else
        	{
        		$data = array('upload_data' => $this->upload->data());
            	$post['c_imgpath'] = base_url() . 'uploads/class_img/' . $data['upload_data']['file_name'];;
            	//upload & filter
            	$this->load->model('Seller_model', 'my_sell');
            	$ret = $this->my_sell->upload_img(filter($post, $members));

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

	/**
	*卖家版-点击课程进入课程详情
	*/
	public function cou_entercou()
	{
		//config
		$members = array('token','c_id');

		try
		{
			//get post
			$post = get_post();
			if(empty($post))
			{
				$post = array('c_id' => $this->input->post('c_id'));
			}

			//get token
			$post['token'] = get_token();

			//check form
			$this->load->library('form_validation');
			$this->form_validation->set_data($post);
			if( ! $this->form_validation->run('entercou'))
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
			
			//filter && return data
			$this->load->model('Seller_model','my_sell');
			$ret = $this->my_sell->cou_entercou($post);

		}catch(Exception $e)
		{
			output_data($e->getCode(),$e->getMessage(),array());
			return;
		}

		//return 
		output_data(400,'获取成功',$ret);
	}
	
}

?>