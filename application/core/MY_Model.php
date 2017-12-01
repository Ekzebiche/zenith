<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model{

	protected $table = '';

	// получение всех записей из таблицы
	public function getAll($condition = array(), $limit = 0){
		$this->db->where($condition);
		if($limit){
			$records = $this->db->get($this->table, $limit);
		} else {
			$records = $this->db->get($this->table);
		}

		return $records->result_array();
	}

	// получение одной записи
	public function get($condition){
		$this->db->where($condition);
		$result = $this->db->get($this->table);
		return $result->row_array();
	}

	// вставка данных в таблицу
	public function insert($data){
		$created_at = new DateTime();
		$data['created_at'] = $created_at->format('Y-m-d H:i:s');
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	// метод обновления данных в таблице
	public function update($condition, $data){
		$updated_at = new DateTime();
		$data['updated_at'] = $updated_at->format('Y-m-d H:i:s');
		$this->db->update($this->table, $data, $condition);
		return $this->db->affected_rows();
	}

	// удаление записи из таблицы
	public function delete($condition){
		$this->db->delete($this->table, $condition);
		return $this->db->affected_rows();
	}
	//проверка есть ли такая запись
	public function checkId($id){
		$this->db->select('id');
		$this->db->where('id', $id);
		$result = $this->db->get($this->table);
		if($result->num_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	
	public function checkInfo($title){
		$this->db->select('title, active');
		$this->db->where('active', 1);
		$this->db->where('title', $title);
		$result = $this->db->get($this->table);
		if($result->num_rows() > 0){
			return false;
		}else{
			return true;
		}
	}
	public function checkInfoEdit($title){
		$this->db->select('title, active');
		$this->db->where('active', 1);
		$this->db->where('title', $title);
		$result = $this->db->get($this->table);
		if($result->num_rows() > 0){
			return false;
		}else{
			return true;
		}
	}
	
	public function savePositionBd($id, $data){
		$this->db->where('id', $id);
		$this->db->update($this->table, $data);
	}
	public function get_header($page = null){
        $this->db->where('page_id', $page);
        $result = $this->db->get('headers');
        return $result->row_array();
    }
    public function get_header_front($page = null){
        $this->db->where('page_id', $page);
        $this->db->where('active', 1);
        $result = $this->db->get('headers');
        return $result->row_array();
    }
    public function delete_header($id){
    	$this->db->where('id', $id);
    	$this->db->delete('headers');
    }
    public function insert_header($data){
    	$this->db->insert('headers', $data);
		return $this->db->insert_id();
    }
    public function update_header($id, $update){
    	$this->db->update('headers', $update, $id);
		return $this->db->affected_rows();
    }
}