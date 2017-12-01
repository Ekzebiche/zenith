<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prices extends MY_Controller {

	public function index(){
		$this->load->model('Prices_model');
		$this->load->model('Page_model');

		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'prices')));
		$this->setData('header', $this->Page_model->get_header_front($this->data['info_page']['id']));
		$this->setData('prices_active', 'header__link__current');
		$result_prices = $this->Prices_model->getAll();
		$images = $this->Prices_model->get_all_images();
		
		foreach($result_prices as $item){
			$prices[$item['id']] = $item;
		}

		foreach($images as $item){
			if(isset($prices[$item['price_id']])){
				$image[$item['price_id']][] = $item;
				$prices[$item['price_id']]['images'][] = $item;
			}
		}
		$this->setData('prices', $prices);
		$this->display('frontend/prices-page/index');
	}
}