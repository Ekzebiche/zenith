<?php    
defined('BASEPATH') OR exit('No direct script access allowed');

class News_admin extends MY_Model{
	protected $table = 'posts';

	public function getImages($id){
		$this->db->where('postId', $id);
		$this->db->order_by('position', 'ASC');
		return $result = $this->db->get('images')->result_array();
	}
	public function get_image($id){
		$this->db->where('id', $id);
		$result = $this->db->get('images');
		return $result->row_array();
	}
}