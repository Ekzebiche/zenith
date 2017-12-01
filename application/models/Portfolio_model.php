<?php    
defined('BASEPATH') OR exit('No direct script access allowed');

class Portfolio_model extends MY_Model{

	protected $table = 'project';

	public function get_portfolio(){
		$this->db->order_by('id', 'ASC');
		$result = $this->db->get($this->table);
    	return $result->result_array();
	}
	public function get_images(){
		$this->db->order_by('position', 'ASC');
		$result = $this->db->get('project_img');
    	return $result->result_array();
	}
}