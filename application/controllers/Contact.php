<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends MY_Controller {

	public function index(){
		$this->load->model('Contact_model');
		$this->load->model('Page_model');

		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'contact')));
		$this->setData('header', $this->Page_model->get_header_front($this->data['info_page']['id']));
		$this->setData('contact_active', 'header__link__current');
		$this->display('frontend/contact-page/index');
	}
}
