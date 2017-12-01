<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company extends MY_Controller {

	public function index(){
		$this->load->model('Company_model');
		$this->load->model('Page_model');
		$this->load->model('Main_admin');

		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'company')));
		$this->setData('header', $this->Page_model->get_header_front($this->data['info_page']['id']));
		$this->setData('company_active', 'header__link__current');
		$this->setData('text_info_3', $this->Main_admin->get_text_info($this->data['info_page']['id'], 3));
		$this->setData('text_info_4', $this->Main_admin->get_text_info($this->data['info_page']['id'], 4));
		$this->display('frontend/company-page/index');
	}
}