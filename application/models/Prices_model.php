<?php    
defined('BASEPATH') OR exit('No direct script access allowed');

class Prices_model extends MY_Model{

	protected $table = 'prices';
	public function get_all_images(){
		$this->db->order_by('position', 'ASC');
		$result = $this->db->get('prices_img');
    	return $result->result_array();
	}
}