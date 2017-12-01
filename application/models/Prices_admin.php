<?php    
defined('BASEPATH') OR exit('No direct script access allowed');

class Prices_admin extends MY_Model{
	protected $table = 'prices';

	public function add_price($data){
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	public function edit_price($id, $update){
		$this->db->where('id', $id);
		$this->db->update($this->table, $update);
		return true;
	}
	public function delete_project($id = null){
		$this->db->where('id', $id);
		$this->db->delete($this->table);
		return true;
	}
	public function get_images($id = null){
		$this->db->where('price_id', $id);
		$this->db->order_by('position', 'ASC');
		return $result = $this->db->get('prices_img')->result_array();
	}
	public function get_image($id = null){
		$this->db->where('id', $id);
		$result = $this->db->get('prices_img');
		return $result->row_array();
	}
}