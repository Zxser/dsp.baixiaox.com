<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | Launch_model Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class Launch_model extends ADLINKX_Model {
	private $db;
	private $table_name;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
		$this->table_name = 'diy_plan';
	}

	public function add($data = array()) {
		$plan_id = $this->get_seq_id();
		$add_data = array(
			'plan_id' => $plan_id,
			'plan_name' => $data['plan_name'],
			'plat_id' => 134,
			'plat_name' => '秒针',
			'type' => 1,
			'shop_id' => $data['shop_id'],
			'ad_type' => '图片',
			'uid' => $data['uid'],
			'is_del' => 0,
//			'status'	=> strtotime($startdate) <= time() ? 1 : 0,
			'status' => 4, //待完善
			'bes_category' => 0,
			'budget' => $data['budget'],
			'device' => $data['device'],
			'startdate' => empty($data['startdate']) ? '1970-1-1' : $data['startdate'],
			'enddate' => empty($data['enddate']) ? '1970-1-1' : $data['enddate'],
			'plan_create_time' => date('Y-m-d H:i:s'),
		);
		$query = $this->db->insert($this->table_name, $add_data);
		return $query && $this->db->affected_rows() > 0 ? $plan_id : false;
	}

	public function get($where = array()) {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($where);
		$query = $this->db->get();
		return $query && $query->num_rows() > 0 ? $query->row_array() : array();
	}

	public function lists($where = array(), $num = 20, $offset = 1, $key = 'id', $stor = 'desc', $fields = '*', &$count) {
		if (!isset($where['shop_id'])) {
			$count_sql = 'SELECT COUNT(*) AS `count` FROM (SELECT shop_id FROM `huihe_marketing_system`.`store` WHERE own_id=' . $where['uid'] . ' AND is_del=0) AS `s` LEFT JOIN `huihe_marketing_system`.`diy_plan` AS `dp` ON `dp`.`shop_id`=`s`.`shop_id` AND `dp`.`uid`=' . $where['uid'] . ' AND `dp`.`is_del`=' . $where['is_del'];
			// $sql = 'SELECT * FROM (SELECT shop_id FROM `huihe_marketing_system`.`store` WHERE own_id='.$where['uid'].' AND is_del=0) AS `s` LEFT JOIN `huihe_marketing_system`.`diy_plan` AS `dp` ON `dp`.`shop_id`=`s`.`shop_id` AND `dp`.`uid`='.$where['uid'].' AND `dp`.`is_del`='.$where['is_del'].' ORDER BY `dp`.`'.$key.'` '.$stor.' LIMIT '. intval(($offset-1)*$num) .','.$num;

			$sql = "SELECT `p`.`plan_id`, `p`.`plan_name`, `p`.`plat_id`, `p`.`plat_name`, `p`.`shop_id`, `p`.`uid`, `p`.`status`, `p`.`budget`, `p`.`device`, `p`.`startdate`, `p`.`enddate`, SUM(`ds`.`pv`) AS `pv`, SUM(`ds`.`click`) AS `click`, SUM(`ds`.`charge`) AS `charge`, IFNULL(SUM(`ds`.`click`)/SUM(`ds`.`pv`)*100, 0) AS `ctr`, IFNULL(SUM(`ds`.`charge`)/SUM(`ds`.`click`), 0) AS `click_cost` FROM (`huihe_marketing_system`.`diy_plan` AS `p`) LEFT JOIN `huihe_marketing_system`.`store` AS `s` ON s.shop_id=p.shop_id LEFT JOIN `huihe_marketing_system`.`dsp_stats_ad_task` AS `ds` ON ds.date BETWEEN '" . $where['start_date'] . "' AND '" . $where['end_date'] . "' AND ds.plan_id=p.plan_id WHERE `p`.`uid` =  '" . $where['uid'] . "' AND `p`.`plat_id` =  '134' AND `p`.`is_del` =  '0' GROUP BY `p`.`plan_id` ORDER BY `p`.`status` desc LIMIT 20";
		} else {
			$count_sql = 'SELECT COUNT(*) AS `count` FROM `huihe_marketing_system`.`diy_plan` WHERE shop_id = "' . $where['shop_id'] . '"';
			// $sql = 'SELECT * FROM `huihe_marketing_system`.`'.$this->table_name.'` '.$this->build_where($where).' ORDER BY '.$key.' '.$stor.' LIMIT '.intval(($offset-1)*$num).','.$num;
			$sql = "SELECT `p`.`plan_id`, `p`.`plan_name`, `p`.`plat_id`, `p`.`plat_name`, `p`.`shop_id`, `p`.`uid`, `p`.`status`, `p`.`budget`, `p`.`device`, `p`.`startdate`, `p`.`enddate`, SUM(`ds`.`pv`) AS `pv`, SUM(`ds`.`click`) AS `click`, SUM(`ds`.`charge`) AS `charge`, IFNULL(SUM(`ds`.`click`)/SUM(`ds`.`pv`)*100, 0) AS `ctr`, IFNULL(SUM(`ds`.`charge`)/SUM(`ds`.`click`), 0) AS `click_cost` FROM (`huihe_marketing_system`.`diy_plan` AS `p`) LEFT JOIN `huihe_marketing_system`.`store` AS `s` ON s.shop_id=p.shop_id LEFT JOIN `huihe_marketing_system`.`dsp_stats_ad_task` AS `ds` ON ds.date BETWEEN '" . $where['start_date'] . "' AND '" . $where['end_date'] . "' AND ds.plan_id=p.plan_id WHERE `p`.`uid` =  '" . $where['uid'] . "' AND `p`.`plat_id` =  '134' AND `p`.`shop_id` =  '" . $where['shop_id'] . "' AND `p`.`is_del` =  '0' GROUP BY `p`.`plan_id` ORDER BY `p`.`status` desc LIMIT 20";
		}
		// var_dump($count_sql);
		$count_query = $this->db->query($count_sql);
		$count = $count_query && $count_query->num_rows() > 0 ? $count_query->result_array()[0]['count'] : 0;
		// $count = 1;
		// var_dump($sql);
		$query = $this->db->query($sql);
		// var_dump($this->db->last_query());
		return $query && $query->num_rows() > 0 ? $query->result_array() : array();
	}

	public function get_all($where) {
		$sql = 'select * from `huihe_marketing_system`.`' . $this->table_name . '` ' . $this->build_where($where);
		// var_dump($sql);
		$query = $this->db->query($sql);
		return $query && $query->num_rows() > 0 ? $query->result_array() : array();
	}

	public function update($data = array(), $where = array()) {
		$this->db->where($where);
		$query = $this->db->update($this->table_name, $data);
		return $query && $this->db->affected_rows() > 0 ? true : false;
	}

	public function delete($where = array()) {
		$FB = null;
		for ($i = 0; $i < count($where); $i++) {
			$FB = $this->update(array('is_del' => 1), $where[$i]);
		}
		return $FB;
	}

	public function build_where($where = array()) {
		$tmp = '';
		foreach ($where AS $k => $v) {
			if ($v != '') {
				if ($k == 'plan_name') {
					$tmp .= '`' . $k . '`' . ' like "%' . $v . '%" and ';
				} else {
					$tmp .= '`' . $k . '`' . '=' . $v . ' and ';
				}
			}
		}
		return !empty($tmp) ? 'where ' . substr($tmp, 0, intval(strlen($tmp) - 5)) : '';
	}
}