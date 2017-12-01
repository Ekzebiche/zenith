<?php    
defined('BASEPATH') OR exit('No direct script access allowed');

class Portfolio_admin extends MY_Model{
	protected $table = 'project';

	public function add_project($data){
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update_project($id, $update){
		$this->db->where('id', $id);
		$this->db->update($this->table, $update);
		return true;
	}
	public function get_images($id){
		$this->db->where('project_id', $id);
		$this->db->order_by('position', 'ASC');
		return $result = $this->db->get('project_img')->result_array();
	}
	public function get_image($id){
		$this->db->where('id', $id);
		$result = $this->db->get('project_img');
		return $result->row_array();
	}
}