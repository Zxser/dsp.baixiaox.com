<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | Creative_model Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class Creative_model extends ADLINKX_Model {
	private $db;
	private $table_name;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
		$this->table_name = 'diy_ad_task';
		$this->load->model('store_model', 'store');
		$this->load->model('launch_model', 'launch');
		$this->load->model('strategy_model', 'strategy');
		$this->load->model('user_model', 'user');
		$this->load->model('diy_board_model', 'diy_board');
	}

	public function add($data = array()) {
		// var_dump($data);
		$ad_task_id = $this->get_seq_id();
		// var_dump($ad_task_id);
		$unit = $this->strategy->get(array('unit_id' => $data['unit_id']));
		// var_dump($unit);
		if ($unit === FALSE) {
			throw new DSPException('策略不存在');
		}
		// $plan = $this->launch->get();
		// var_dump($plan);
		// if ($plan === FALSE) {
		// 	throw new DSPException('投放不存在');
		// }
		// $mz_audit_stat = $plan['device'] == 1 ? 1 : 0;
		$board = $this->diy_board->get(array('uid' => $data['uid'], 'board_id' => $data['board_id']));
		// var_dump($board);
		if ($board === FALSE) {
			throw new DSPException('素材图片不存在');
		}
		$plat_name = '独立网站';
		$kid = "{$unit['shop_id']},{$unit['plan_id']},{$unit['unit_id']},{$board['board_id']}";
		$plat_id = '134';
		$k_url = $j_url = $data['url'];
		$query = $this->db->insert($this->table_name, array(
			'id' => $ad_task_id,
			'shop_id' => $unit['shop_id'],
			'plat_id' => $plat_id,
			'plat_name' => $plat_name,
			'plan_id' => $unit['plan_id'],
			'plan_name' => $unit['plan_name'],
			'unit_id' => $unit['unit_id'],
			'unit_name' => $unit['unit_name'],
			'borad_id' => $board['board_id'],
			'borad_name' => $data['borad_name'],
			'borad_url' => $data['url'],
			'kid' => $kid,
			'k_url' => $k_url,
			'j_url' => $j_url,
			'wap_url' => '',
			'add_time' => date('Y-m-d H:i:s'),
			'is_del' => 0,
			'uid' => $data['uid'],
			'pic_path' => $data['pic_path'] === FALSE ? $board['pic_path'] : $data['pic_path'],
			'pic_width' => $data['pic_width'],
			'pic_height' => $data['pic_height'],
			'status' => 0,
			'pic_size' => $data['pic_width'] . '*' . $data['pic_height'],
			'gxb_monitor_url' => isset($data['monitor_url']) ? $data['monitor_url'] : '',
			'mz_audit_stat' => 1,
		));
		return $query && $this->db->affected_rows() > 0 ? $ad_task_id : false;
	}

	public function get($where = array(), $fields = '*') {
		$this->db->select($fields);
		$this->db->from($this->table_name);
		$this->db->where($where);
		$query = $this->db->get();
		return $query && $query->num_rows() > 0 ? $query->row_array() : array();
	}

	public function lists($where = array(), $offset = 0, $num = 20, $key = 'id', $stor = 'desc', $fields = '*', &$count) {
		$count_sql = "SELECT COUNT(*) AS `count` FROM (`huihe_marketing_system`.`diy_ad_task` AS `dat`) LEFT JOIN `huihe_marketing_system`.`store` AS `s` ON s.shop_id = dat.shop_id LEFT JOIN `huihe_marketing_system`.`dsp_stats_ad_task` AS `ds` ON ds.date BETWEEN '".$where['start_date']."' AND '".$where['end_date']."' AND ds.ad_task_id = dat.borad_id WHERE `dat`.`uid` = '" . $where['uid'] . "' " . (isset($where['shop_id']) ? "AND `dat`.`shop_id`=" . $where['shop_id'] : '') . " AND `dat`.`plat_id` = '134' " . (isset($where['unit_id']) ? "AND `dat`.`unit_id`=" . $where['unit_id'] : '') . " AND `dat`.`is_del` = '0' GROUP BY `dat`.`borad_id` ORDER BY `dat`.`status`";
		// var_dump($count_sql);
		$count_query = $this->db->query($count_sql);
		$count = $count_query && $count_query->num_rows() > 0 ? $count_query->result_array()[0]['count'] : 0;
		$sql = "SELECT `dat`.`id`,`dat`.`borad_id`, `dat`.`borad_name`, `dat`.`unit_id`, `dat`.`unit_name`, `dat`.`plan_id`, `dat`.`plan_name`, `dat`.`shop_id`, `dat`.`plat_id`, `dat`.`plat_name`, `dat`.`uid`, `dat`.`status`, `dat`.`pic_path`,`dat`.`pic_size`,`dat`.`borad_url`, `s`.`shop_title`, IFNULL(SUM(`ds`.`pv`), 0) AS 'pv', IFNULL(SUM(`ds`.`click`), 0) AS 'click', IFNULL(SUM(`ds`.`charge`), 0) AS 'charge', IFNULL(SUM(`ds`.`click`) / SUM(`ds`.`pv`) * 100, 0) AS `ctr`, IFNULL(SUM(`ds`.`charge`) / SUM(`ds`.`click`), 0) AS `click_cost` FROM (`huihe_marketing_system`.`diy_ad_task` AS `dat`) LEFT JOIN `huihe_marketing_system`.`store` AS `s` ON s.shop_id = dat.shop_id LEFT JOIN `huihe_marketing_system`.`dsp_stats_ad_task` AS `ds` ON ds.date BETWEEN '".$where['start_date']."' AND '".$where['end_date']."' AND ds.ad_task_id = dat.borad_id WHERE `dat`.`uid` = '" . $where['uid'] . "' " . (isset($where['shop_id']) ? "AND `dat`.`shop_id`=" . $where['shop_id'] : '') . " AND `dat`.`plat_id` = '134' " . (isset($where['unit_id']) ? "AND `dat`.`unit_id`=" . $where['unit_id'] : '') . " AND `dat`.`is_del` = '0' GROUP BY `dat`.`borad_id` ORDER BY `dat`.`status` DESC LIMIT " . intval(($offset - 1) / $num) . "," . $num;
		// var_dump($sql);
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

	public function edit_creative($data, $where) {
		$creative_data = array(
			'borad_name' => $data['borad_name'],
			'borad_url' => $data['url'],
			'gxb_monitor_url' => isset($data['monitor_url']) ? $data['monitor_url'] : '',
			'pic_path' => $data['pic_path'],
			'pic_width' => $data['pic_width'],
			'pic_height' => $data['pic_height'],
			'pic_size' => $data['pic_width'] . '*' . $data['pic_height'],
			'k_url' => $data['url'],
			'j_url' => $data['url'],

		);
		return $this->update($creative_data, $where);
	}

	public function build_where($where = array()) {
		$tmp = '';
		foreach ($where AS $k => $v) {
			if ($v != '') {
				if ($k == 'borad_name') {
					$tmp .= '`' . $k . '`' . ' like "%' . $v . '%" and ';
				} else {
					$tmp .= '`' . $k . '`' . '=' . $v . ' and ';
				}

			}
		}
		return $tmp == '' ? '' : 'where ' . substr($tmp, 0, intval(strlen($tmp) - 5));
	}
}