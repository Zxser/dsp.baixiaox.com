<?php
/**
 * |---------------------------------------------------
 * | Loger_model Model
 * |---------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class Loger_model extends ADLINKX_Model {
	private $db;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
	}

	public function add($data = array()) {
		$query = is_array($data) && !empty($data) ? $this->db->insert('operate_log', $data) : false;
		// var_dump($this->db->last_query());
		return $query ? true : false;
	}

	public function update($data = array(), $where = array()) {

	}

	public function get($where = array()) {

	}

	public function lists($where = array(), $num = 20, $offset = 1, $key = 'id', $sort = 'DESC', $fields = array('*'), &$count) {
		$count_sql = 'select count(*) as `count` from `huihe_marketing_system`.`dsp_log_charge` '.$this->build_where($where);
		$count = $this->db->query($count_sql)->result_array()[0]['count'];
		$this->db->select(implode(',', $fields));
		$this->db->from('dsp_log_charge');
		$this->db->where($where);
		$this->db->limit(intval($num * ($offset - 1)));
		$this->db->order_by($key, $sort);
		// var_dump($this->db->last_query());
		$query = $this->db->get();

		return $query && $query->num_rows() > 0 ? $query->result_array() : array();
	}

	public function delete($where = array()) {
		$this->db->where($where);
		$query = $this->db->delete('dsp_log_charge');
		return $query && $this->db->affected_rows() > 0? true : false;
	}

	public function build_where($where = array()){
		$tmp = '';
		foreach($where AS $k => $v){
			$tmp .= '`' . $k . '`' . '=' . $v .' and ';
		}
		return 'where '. substr($tmp,0,intval(strlen($tmp)-5));
	}

	public function add_operation_log($data){
		$query = $this->db->insert('dsp_user_operation_record',$data);
		return $query && $this->db->affected_rows() > 0 ? true : false;
	}
}