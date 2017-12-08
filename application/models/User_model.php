<?php
/**
 * |---------------------------------------------------
 * | User_model Model
 * |---------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class User_model extends ADLINKX_Model {
	private $aliyun_db;
	private $salt;
	public function __construct() {
		parent::__construct();
		$this->aliyun_db = $this->get_database('aliyun');
		$this->salt = $this->config->item('salt');
	}

	public function add($data) {
		$this->aliyun_db->query("REPLACE INTO sequence.sequence (`stub`) VALUES ('a');");
		$uid = $this->aliyun_db->insert_id();
		$encrypted_pw = $this->encrypt_password($data['password']);
		$user_info = array(
			'shop_site' => '',
			'enable' => 0,
			'add_time' => date('Y-m-d H:i:s',time()),
			'owner' => '',
			'password' => $encrypted_pw,
			'pid' => 0,
			'uid' => $uid,
			'email' => $data['email'],
			'phone' => $data['phone'],
			'username' => $data['name'],
		);

		$query = is_array($data) && !empty($data) ? $this->aliyun_db->insert('user', $user_info) : false;
		// var_dump($this->db->last_query());
		return $query && $this->aliyun_db->affected_rows() > 0 ? true : false;
	}

	public function update($where, $data) {

	}

	public function get($where, $fields = array('*')) {
		$this->aliyun_db->select(implode(',', $fields));
		$this->aliyun_db->where($where);
		$this->aliyun_db->from('user');
		$query = $this->aliyun_db->get();
		// var_dump($this->aliyun_db->last_query());
		return $query && $query->num_rows() > 0 ? $query->row_array() : array();
	}

	public function lists() {

	}

	public function delete($where) {

	}

	/**
	 *
	 * @param string $password 原始密码
	 * @return string
	 */
	private function encrypt_password($password) {
		$encrypted_pw = md5($password . $this->salt);
		return $encrypted_pw;
	}
}