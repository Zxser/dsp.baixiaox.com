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
class Dsp_stats_ad_task_model extends ADLINKX_Model {
	private $db;
	private $table_name;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
		$this->table_name = 'dsp_stats_ad_task';
	}

	public function query_list($where){
		$sql = 'SELECT `ds`.`hour`, SUM(`ds`.`pv`) AS `pv`, SUM(`ds`.`click`) AS `click`, SUM(`ds`.`charge`) AS `charge`, IFNULL(SUM(`ds`.`click`)/SUM(`ds`.`pv`)*100, 0) AS `ctr`, IFNULL(SUM(`ds`.`charge`)/SUM(`ds`.`click`), 0) AS `click_cost` FROM (`huihe_marketing_system`.`dsp_stats_ad_task` AS `ds`) WHERE `ds`.`uid` =  0 AND `ds`.`date` >= "'.$where['start_date'].'" AND `ds`.`date` <= "'.$where['end_date'].'" AND `ds`.`store_id` =  "'.$where['shop_id'].'" GROUP BY `ds`.`hour`';
		var_dump($sql);
	}
}