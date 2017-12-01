<?php    
defined('BASEPATH') OR exit('No direct script access allowed');

class News_model extends MY_Model{
	protected $table = 'page_info';

	public function get_blogs(){
		$this->db->select('posts.id, posts.title, posts.pretext, posts.folder, images.name as image');
		$this->db->order_by('posts.id', 'DESC');
    	$this->db->from('posts');
        $this->db->join('images', 'images.postId = posts.id', 'left');
        $this->db->group_by('posts.id');
    	$result = $this->db->get();
    	return $result->result_array();
	}
	public function get_info($id){
		$this->db->where('id', $id);
		$result = $this->db->get('posts');
		return $result->row_array();
	}

	public function get_info_images($id){
		$this->db->where('postId', $id);
		$result = $this->db->get('images');
		return $result->result_array();
	}

}