<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function index(){
		$this->login_check();
		$this->load->model('Dashboard_admin');

		$this->setData('title','Статистика');
		$this->display('backend/dashboard-page/index');
		$_SESSION['message'] = '';
	}
}