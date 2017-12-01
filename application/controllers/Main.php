<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

	public function index(){
		$this->load->model('Main_admin');
		$this->load->model('Page_model');

		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'main')));
		$this->setData('header', $this->Page_model->get_header_front($this->data['info_page']['id']));
		$this->setData('text_info_1', $this->Main_admin->get_text_info($this->data['info_page']['id'], 1));
		$this->setData('text_info_2', $this->Main_admin->get_text_info($this->data['info_page']['id'], 2));
		$this->setData('sliders', $this->Main_admin->get_slider());
		$this->setData('blog', $this->Main_admin->get_blog_main());

		$result_portfolio = $this->Main_admin->get_portfolio_main();
		$images = $this->Main_admin->get_portfolio_images();
		
		foreach($result_portfolio as $item){
			$portfolio[$item['id']] = $item;
		}

		foreach($images as $item){
			if(isset($portfolio[$item['project_id']])){
				$image[$item['project_id']][] = $item;
				$portfolio[$item['project_id']]['images'][] = $item;
			}
		}
		$this->setData('image', $image);
		$this->setData('portfolio', $portfolio);

		$this->display('frontend/first-page/index');
		
	}
}
