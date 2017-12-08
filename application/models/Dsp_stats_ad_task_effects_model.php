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
class Dsp_stats_ad_task_effects_model extends ADLINKX_Model {
	private $db;
	private $table_name;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
		$this->table_name = 'dsp_stats_ad_task_effects';
	}

	/**
	 * 查询店铺消耗数据
	 *
	 * @param int	$store_id	查询的店铺ID
	 * @param string $startdate	开始日期
	 * @param string $enddate	结束日期
	 * @return double	店铺消耗金额
	 */
	public function query_store_charge($store_id, $startdate, $enddate) {
		$this->db->from($this->table_name . ' AS `dse`');
		$this->db->select('SUM(`dse`.`ds_charge`) AS `charge`');
		$this->db->where(array('`dse`.`date` >=' => $startdate, '`dse`.`date` <=' => $enddate, '`dse`.`store_id`' => $store_id));
		$query = $this->db->get();
		// var_dump($this->db->last_query());
		$data = $query->row_array();
		return is_array($data) ? round($data['charge'] / 100, 2) : 0;
	}

	/**
	 * 查询店铺成交数据
	 *
	 * @param int	$store_id	查询的店铺ID
	 * @param string $startdate	开始日期
	 * @param string $enddate	结束日期
	 * @return double	店铺成交金额
	 */
	public function query_store_achieve_payment($store_id, $startdate, $enddate) {
		$this->db->from($this->table_name . ' AS `dse`');
		$this->db->select('SUM(`dse`.`achieve_payment`) AS `achieve_payment`');
		$this->db->where(array('`dse`.`date` >=' => $startdate, '`dse`.`date` <=' => $enddate, '`dse`.`store_id`' => $store_id));
		$query = $this->db->get();
		// var_dump($this->db->last_query());
		$data = $query->row_array();
		return is_array($data) ? round($data['achieve_payment'], 2) : 0;
	}
}