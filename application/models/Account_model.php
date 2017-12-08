<?php
/**
 * |---------------------------------------------------
 * | Account_model Model
 * |---------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date 2017-01-07
 * |--------------------------------------------------------------
 */
class Account_model extends ADLINKX_Model {
	private $db;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
	}

	public function add($data = array()){

	}

	public function get($where = array()){
		$this->db->select('*');
		$this->db->from('user');
		$this->db->where($where);
		$query = $this->db->get();
		return $query && $query->num_rows() > 0 ? $query->result_array()[0]: array() ;
	}

	public function lists($where = array(), $num = 0, $offset = 20, $key = 'id', $stor = 'desc', $fields = '*'){

	}

	public function update($data = array(), $where = array()){
		$this->db->where($where);
		$query = $this->db->update('user',$data);
		return $query && $this->db->affected_rows() > 0 ? true : false;
	}

	public function delete($where = array()){

	}

	public function recharge($data,$where){
		$status = null;
		$org_monery = $this->get_org_money($where['uid']);
		$money = $data['money']+$org_monery;
		$this->db->where($where);
		$query = $this->db->update('user',array('money' => $money));
		$log = array(
			'uid' => $where['uid'],
			'date' => date('Y-m-d',time()),
			'time' => date('Y-m-d H:i:s',time()),
			'money' => $data['money'],
			'type' => 1,
			'user_money' => $money,
			'remark' => '手工充值',
			'detail' => '[]',
		);

		if($query && $this->db->affected_rows() > 0){
			//记录充值日志
			$status = $this->db->insert('dsp_log_charge',$log) ? true : false;
		}
		return ($query && $this->db->affected_rows() > 0) && $status ? true : false;
	}

	public function get_org_money($uid){
		$this->db->select('money');
		$this->db->from('user');
		$this->db->where(array('uid' => $uid));
		$query = $this->db->get();
		return $query && $query->num_rows() > 0 ? $query->result_array()[0]['money']: 0;
	}
}