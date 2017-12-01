<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends MY_Controller {

	public function index(){
		$this->load->model('News_model');
		$this->load->model('Page_model');
		
		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'news')));
		$this->setData('header', $this->Page_model->get_header_front($this->data['info_page']['id']));
		$this->setData('blogs', $this->News_model->get_blogs());
		$this->setData('blog_active', 'header__link__current');
		$this->display('frontend/news-page/index');
	}
	public function reed($id){
		$this->load->model('News_model');
		$this->load->model('Page_model');
		
		$this->setData('blog_active', 'header__link__current');
		$this->setData('info', $this->News_model->get_info($id));
		$page = array(
			'title' => $this->data['info']['title'],
			'keywords' => $this->data['info']['keywords'],
			'description' => $this->data['info']['description'],
		);
		$this->setData('info_page', $page);
		$this->setData('images', $this->News_model->get_info_images($id));
		$this->display('frontend/news-page/show');
	}
}
