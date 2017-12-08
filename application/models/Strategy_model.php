<?php
/**
 * |---------------------------------------------------
 * | Strategy_model Model
 * |---------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date 2017-01-07
 * |--------------------------------------------------------------
 */
class Strategy_model extends ADLINKX_Model {
	private $db;
	private $table_name;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
		$this->table_name = 'diy_unit';
		$this->load->model('dsp_rtb_rules_model', 'drrm');
		$this->load->model('launch_model', 'launch');
	}

	public function add($data = array()) {
		$plan = $this->launch->get(array('plan_id' => $data['plan_id']));
		$unit_id = $this->get_seq_id();
		$set_data = array(
			'unit_id' => $unit_id,
			'unit_name' => $data['unit_name'],
			'plan_id' => $plan['plan_id'],
			'plan_name' => $plan['plan_name'],
			'type' => 1,
			'shop_id' => $plan['shop_id'],
			'plat_id' => 134,
			'plat_name' => '秒针',
			'uid' => $plan['uid'],
			'is_del' => 0,
			'status' => 0,
			'price' => $data['price'],
			'unit_create_time' => date('Y-m-d H:i:s'),
			'tags_value' => $data['tags_value'],
		);
		// var_dump($set_data);
		$add_unit = $this->db->insert($this->table_name, $set_data);
		// 添加一条竟价规则
		$add_rules = $this->drrm->set($data['plan_id'], $data['uid'], $unit_id, json_decode($data['date_value']));
		return $add_unit && $add_rules ? $unit_id : false;
	}

	public function get($where = array(), $fields = '*') {
		$this->db->select($fields);
		$this->db->from($this->table_name);
		$this->db->where($where);
		$query = $this->db->get();
		return $query && $query->num_rows() > 0 ? $query->row_array() : array();
	}

	public function lists($where = array(), $num = 20, $offset = 1, $key = 'id', $stor = 'desc', $fields = '*', &$count) {
		// $count_sql = "SELECT COUNT(*) AS `count` FROM (`diy_unit` AS `u`) LEFT JOIN `store` AS `s` ON s.shop_id = u.shop_id LEFT JOIN `dsp_stats_ad_task` AS `ds` ON ds.date BETWEEN '" . $where['start_date'] . "' AND '" . $where['end_date'] . "' AND ds.unit_id = u.unit_id WHERE `u`.`uid` = '" . $where['uid'] . "' AND `u`.`plat_id` = '134' " . (isset($where['shop_id']) ? "AND `u`.`shop_id` = '" . $where['shop_id'] . "'" : '') . " AND `u`.`is_del` = '0' " . (isset($where['plan_id']) ? "AND `u`.`plan_id` = '" . $where['plan_id'] . "'" : '') . " GROUP BY `u`.`unit_id` ORDER BY `u`.`status`";

		$count_sql = 'select count(*) as count from `diy_unit` where uid="'.$where['uid'].'"'.(isset($where['plan_id']) ? 'and plan_id="'.$where['plan_id'].'"': '').(isset($where['start_date']) || isset($where['end_date']) ? 'and unit_create_time between "'.$where['start_date'].'" and "'.$where['end_date'].'"': '');
		//unit_create_time BETWEEN "" and "" ';
		// var_dump($count_sql);
		$count_qurty = $this->db->query($count_sql);
		$count = $count_qurty && $count_qurty->num_rows() > 0 ? $count_qurty->result_array()[0]['count'] : 0;
		$sql = "SELECT `u`.`unit_id`, `u`.`unit_name`, `u`.`plan_id`, `u`.`plan_name`, `u`.`type`, `u`.`shop_id`, `u`.`plat_id`, `u`.`plat_name`, `u`.`uid`, `u`.`status`, `u`.`price`, `s`.`shop_title`, SUM(`ds`.`pv`) AS `pv`, SUM(`ds`.`click`) AS `click`, SUM(`ds`.`charge`) AS `charge`, IFNULL(SUM(`ds`.`click`) / SUM(`ds`.`pv`) * 100, 0) AS `ctr`, IFNULL(SUM(`ds`.`charge`) / SUM(`ds`.`click`), 0) AS `click_cost` FROM (`diy_unit` AS `u`) LEFT JOIN `store` AS `s` ON s.shop_id = u.shop_id LEFT JOIN `dsp_stats_ad_task` AS `ds` ON ds.date BETWEEN '" . $where['start_date'] . "' AND '" . $where['end_date'] . "' AND ds.unit_id = u.unit_id WHERE `u`.`uid` = '" . $where['uid'] . "' AND `u`.`plat_id` = '134' " . (isset($where['shop_id']) ? "AND `u`.`shop_id` = '" . $where['shop_id'] . "'" : '') . " AND `u`.`is_del` = '0' " . (isset($where['plan_id']) ? "AND `u`.`plan_id` = '" . $where['plan_id'] . "'" : '') . " GROUP BY `u`.`unit_id` ORDER BY `u`.`status` DESC LIMIT 20";
		// var_dump($sql);
		$query = $this->db->query($sql);
		return $query && $query->num_rows() > 0 ? $query->result_array() : array();
	}

	public function get_all($where) {
		$sql = 'select * from `huihe_marketing_system`.`' . $this->table_name . '` ' . $this->build_where($where);
		$query = $this->db->query($sql);
		return $query && $query->num_rows() > 0 ? $query->result_array() : array();
	}

	public function update($data = array(), $where = array()) {
		$this->db->where($where);
		$query = $this->db->update($this->table_name, $data);
		// var_dump($this->db->last_query());
		return $query && $this->db->affected_rows() > 0 ? true : false;
	}

	public function delete($where = array()) {
		$FB = null;
		for ($i = 0; $i < count($where); $i++) {
			$FB = $this->update(array('is_del' => 1), $where[$i]);
		}
		return $FB;
	}

	public function update_strategy($data, $where) {
		$date_value = $data['date_value'];
		unset($data['date_value']);
		$res = $this->update($data, $where);
		//添加一条竟价规则
		$update_rules = $this->drrm->set($where['plan_id'], $where['uid'], $where['unit_id'], json_decode($date_value));
		return $res && $update_rules ? true : false;
	}

	public function build_where($where = array()) {
		$tmp = '';
		foreach ($where AS $k => $v) {
			if ($v != '') {
				if ($k == 'unit_name') {
					$tmp .= '`' . $k . '`' . ' like "%' . $v . '%" and ';
				} else {
					$tmp .= '`' . $k . '`' . '=' . $v . ' and ';
				}
			}

		}
		return !empty($tmp) ? 'WHERE ' . substr($tmp, 0, intval(strlen($tmp) - 5)) : '';
	}
}