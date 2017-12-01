<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portfolio extends MY_Controller {

	public function index(){
		$this->load->model('Portfolio_model');
		$this->load->model('Page_model');

		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'portfolio')));
		$this->setData('header', $this->Page_model->get_header_front($this->data['info_page']['id']));
		$this->setData('portfolio_active', 'header__link__current');

		$result_portfolio = $this->Portfolio_model->get_portfolio();
		$images = $this->Portfolio_model->get_images();
		
		foreach($result_portfolio as $item){
			$portfolio[$item['id']] = $item;
		}

		foreach($images as $item){
			$image[$item['project_id']][] = $item;
			$portfolio[$item['project_id']]['images'][] = $item;
		}
		$this->setData('image', $image);
		$this->setData('portfolio', $portfolio);

		$this->display('frontend/portfolio-page/index');
	}
}