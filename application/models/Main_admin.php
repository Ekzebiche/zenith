<?php    
defined('BASEPATH') OR exit('No direct script access allowed');

class Main_admin extends MY_Model{

	protected $table = 'page_info';
	public function getImages($id){
		
		$this->db->where('postId', $id);
		$this->db->order_by('position', 'ASC');
		return $result = $this->db->get('images')->result_array();
	}
	public function get_text_info($page_id, $position){
		$this->db->where('page_id', $page_id);
		$this->db->where('position', $position);
		$result = $this->db->get('text_page');
		return $result->row_array();
	}
	public function get_slide($id){
		$this->db->where('id', $id);
		$result = $this->db->get('slider');
		return $result->row_array();
	}
	public function add_slide($insert){
		$this->db->insert('slider', $insert);
		return $this->db->insert_id();
	}
	public function get_slider(){
		$result = $this->db->get('slider');
		return $result->result_array();
	}
	public function edit_slide($update,$id){
		$this->db->update('slider', $update, $id);
		return true;
	}
	public function delete_slide($id){
		$this->db->where('id', $id);
		$this->db->delete('slider');
		return $this->db->affected_rows();
	}
	public function update_slide($id, $update){
		$this->db->where('id', $id);
		$this->db->update('slider', $update);
		return true;
	}
	public function get_portfolio_main_test(){
		$this->db->select('project.id, project.title, project.text, project_img.name as image');
		$this->db->order_by('project.id', 'ASC');
    	$this->db->from('project');
        $this->db->join('project_img', 'project_img.project_id = project.id', 'left');
        $this->db->where('index', 1);
        $this->db->group_by('project.id');
    	$result = $this->db->get();
    	return $result->result_array();
	}
	public function get_portfolio_main(){
		$this->db->where('index', 1);
		$this->db->order_by('id', 'ASC');
		$result = $this->db->get('project');
    	return $result->result_array();
	}
	public function get_portfolio_images(){
		$this->db->order_by('position', 'ASC');
		$result = $this->db->get('project_img');
    	return $result->result_array();
	}
	public function get_blog_main(){
		$this->db->order_by('id', 'DESC');
		$this->db->limit(2);
		$result = $this->db->get('posts');
    	return $result->result_array();
	}
}