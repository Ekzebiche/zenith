<?php    
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends MY_Model{

	protected $table = 'r_users';
	protected $login_attempts = 'login_attempts';

	public function check_login($email){
		$this->db->select('id_user, email');
		$this->db->where('email', $email);
		$this->db->limit(1);

		$query = $this->db->get($this->table);
		if($query->num_rows() == 1){
			$result = $query->row_array();
			return $result['id_user'];
		}else{
			return false;
		}
	}
	//Проверка на брут
	public function check_brute($id){
		$now = time();
    	$valid_attempts = $now - (2 * 60 * 60);
		$this->db->where('user_id', $id);
		$this->db->where('time >', $valid_attempts);
    	$query = $this->db->get($this->login_attempts);
    	if($query->num_rows() < 5){
    		return true;
		}else{
			return false;
		}

	}
	public function get_info($id){
		$this->db->select('id_user, email, password, salt');
		$this->db->where('id_user', $id);
		$this->db->limit(1);
		return $query = $this->db->get($this->table)->row_array();
	}
	//Блокировка пользователя
	public function detected_brute($id){
		$now = time();
		$d = array(
			'user_id'=>$id,
			'time' => $now
		);
		$this->db->insert($this->login_attempts, $d);
	}
	
}