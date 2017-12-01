<?php    
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_model extends MY_Model{
	protected $table = 'page_info';

	public function update_info_text($page,$position,$update){
		$this->db->where('page_id', $page);
		$this->db->where('position', $position);
		$this->db->update('text_page', $update);
		return true;
	}
}