<?php

class Diy_board_model extends ADLINKX_Model {
	private $db;
	private $table_name;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
		$this->table_name = 'diy_board';
	}

	public function add($data){
		$board_id = $this->get_seq_id();
		$data['board_id'] = $board_id;
		$data['url'] = '';
		$data['type'] = 1;
		$query = $this->db->insert($this->table_name, $data);
		return $query && $this->db->affected_rows() > 0 ? $board_id : false;
	}

	public function get($where){
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($where);
		$query = $this->db->get();
		// var_dump($this->db->last_query());
		return $query && $query->num_rows() > 0 ? $query->row_array(): array();
	}
}