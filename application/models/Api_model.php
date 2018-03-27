<?php

class Api_model extends ADLINKX_Model {
	private $db;
	private $dsp_satef_list;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
		$this->dsp_satef_list = array('date', 'edate', 'device', 'store_id', 'plan_id', 'unit_id', 'ad_task_id', 'ds_pv', 'ds_click', 'ds_charge');
	}

	public function query_list($type, $shop_id, $start_date, $end_date, $format, $metric, $offset = 1, $num = 20, $key = 'id', $stor = 'DESC', $fields = '*', &$count) {
		if ($type == 'week') {
			$c = '';
			$where = '';
		} elseif ($type == 'date') {
			$c = 'DATE_FORMAT(`ds`.`date`,"%m") AS `date`';
			$where = 'AND `ds`.`date` LIKE "' . date('Y-', time()) . '%"';
			$group_by = 'DATE_FORMAT(`ds`.`date`,"%m")';
		} else {
			$c = '`ds`.`' . $type . '`';
			$where = 'AND `ds`.`date` >= "' . $start_date . '" AND `ds`.`date` <= "' . $end_date . '"';
			$group_by = '`ds`.`' . $type . '`';
		}
		switch ($metric) {
		case 'pv': //浏览量
			$fields = $c . ',SUM(`ds`.`pv`) AS `pv`';
			break;
		case 'click': //点击
			$fields = $c . ',SUM(`ds`.`click`) AS `click`';
			break;
		case 'charge': //总消耗
			$fields = $c . ',SUM(`ds`.`charge`) AS `charge`';
			break;
		default: //
			$fields = $c . ',`ds`.`hour`,SUM(`ds`.`pv`) AS `pv`,SUM(`ds`.`click`) AS `click`,SUM(`ds`.`charge`) AS `charge`,IFNULL(SUM(`ds`.`click`)/SUM(`ds`.`pv`) * 100,0) AS `ctr`,IFNULL(SUM(`ds`.`charge`) / SUM(`ds`.`click`),0) AS `click_cost`';
			break;
		}
		if ($format == 'chart') {
			$sql = 'SELECT ' . $fields . ' FROM (`huihe_marketing_system`.`dsp_stats_ad_task` AS `ds`) WHERE `ds`.`uid` = "0" ' . $where . ' AND `ds`.`store_id` = "' . $shop_id . '" GROUP BY ' . $group_by;
		} else {
			$count_sql = 'SELECT COUNT(*) AS `count` FROM (`huihe_marketing_system`.`dsp_stats_ad_task` AS `ds`) WHERE `ds`.`uid` = "0" AND `ds`.`date` >= "' . $start_date . '" AND `ds`.`date` <= "' . $end_date . '" AND `ds`.`store_id` = "' . $shop_id . '" GROUP BY `ds`.`' . $type . '`';
			$sql = 'SELECT ' . $fields . ' FROM (`huihe_marketing_system`.`dsp_stats_ad_task` AS `ds`) WHERE `ds`.`uid` = "0" ' . $where . ' AND `ds`.`store_id` = "' . $shop_id . '" GROUP BY `ds`.`' . $type . '`';
		}
		// var_dump($sql);
		$query = $this->db->query($sql);
		return $query && $query->num_rows() > 0 ? $query->result_array() : array();
	}

	public function dsp_satef($type, $shop_id, $start_date, $end_date, $format, $metric, $offset = 1, $num = 20, $key = 'id', $stor = 'DESC', $fields = '*', &$count) {
		$sql = "SELECT `dse`.`store_id`,`dse`.`date`,SUM(`dse`.`referer_pv`) AS `referer_pv`,SUM(`dse`.`pv`) AS `pv`,SUM(`dse`.`sv`) AS `uv`,SUM(`dse`.`pat_users`) AS `pat_users`,SUM(`dse`.`pat_trades`) AS `pat_trades`,SUM(`dse`.`pat_payment`) AS `pat_payment`,SUM(`dse`.`achieve_users`) AS `achieve_users`,SUM(`dse`.`achieve_trades`) AS `achieve_trades`,SUM(`dse`.`achieve_payment`) AS `achieve_payment`,SUM(`dse`.`store_fov`) AS `store_fov`,SUM(`dse`.`item_fov`) AS `item_fov`,SUM(`dse`.`cart`) AS `cart`,SUM(`dse`.`ds_pv`) AS `ds_pv`,SUM(`dse`.`ds_click`) AS `ds_click`,SUM(`dse`.`ds_charge`) AS `ds_charge`,IFNULL(SUM(`dse`.`ds_click`) / SUM(`dse`.`ds_pv`) * 100,0) AS `ds_ctr`,IFNULL(SUM(`dse`.`ds_charge`) / SUM(`dse`.`ds_click`) / 100,0) AS `ds_click_cost`,IFNULL(SUM(`dse`.`loss`) / SUM(`dse`.`uv`) * 100,0) AS `jump_rate`,IFNULL(SUM(`dse`.`pv`) / SUM(`dse`.`uv`), 0) AS `depth`,IFNULL(SUM(`dse`.`achieve_users`) / SUM(`dse`.`uv`) * 100,0) AS `achieve_rate`,IFNULL(SUM(`dse`.`achieve_payment`) / SUM(`dse`.`achieve_users`),0) AS `per_customer_transaction`,IFNULL(SUM(`dse`.`achieve_payment`) / (SUM(`dse`.`ds_charge`) / 100),0) AS `roi`,IFNULL(SUM(`dse`.`store_fov`) / SUM(`dse`.`uv`) * 100,0) AS `store_fov_rate`,IFNULL(SUM(`dse`.`item_fov`) / SUM(`dse`.`uv`) * 100,0) AS `item_fov_rate`,IFNULL(SUM(`dse`.`store_fov` + `dse`.`item_fov`) / SUM(`dse`.`uv`) * 100,0) AS `fov_rate`,IFNULL(SUM(`dse`.`cart`) / SUM(`dse`.`uv`) * 100,0) AS `cart_rate` FROM (`dsp_stats_ad_task_effects` AS `dse`) WHERE `dse`.`store_id` = '" . $shop_id . "' AND `dse`.`date` BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND `dse`.`edate` BETWEEN `dse`.`date` AND DATE_ADD(`dse`.`date`, INTERVAL 0 DAY) GROUP BY `dse`.`date`";
		// var_dump($sql);
		$query = $this->db->query($sql);
		return $query && $query->num_rows() > 0 ? $query->result_array() : array();
	}

	public function get_table_data($type, $uid, $shop_id, $start_date, $end_date, $offset, $num, $key, $stor, $fields, &$count) {
		switch ($type) {
		case 'plan':
			$count_sql = 'SELECT COUNT(*) AS `count` FROM (`huihe_marketing_system`.`dsp_stats_ad_task_effects` AS `df`) WHERE `df`.`store_id`="' . $shop_id . '" AND `df`.`date` LIKE "' . date('Y-m', time()) . '%" GROUP BY `df`.`date`';
			$sql = "SELECT `p`.`plan_id`, `p`.`plan_name`, `p`.`plat_id`, `p`.`plat_name`, `p`.`shop_id`, `p`.`uid`, `p`.`status`, `p`.`budget`, `p`.`device`, `p`.`startdate`, `p`.`enddate`, SUM(`dse`.`referer_pv`) AS `referer_pv`, SUM(`dse`.`pv`) AS `pv`, SUM(`dse`.`sv`) AS `uv`, SUM(`dse`.`pat_users`) AS `pat_users`, SUM(`dse`.`pat_trades`) AS `pat_trades`, SUM(`dse`.`pat_payment`) AS `pat_payment`, SUM(`dse`.`achieve_users`) AS `achieve_users`, SUM(`dse`.`achieve_trades`) AS `achieve_trades`, SUM(`dse`.`achieve_payment`) AS `achieve_payment`, SUM(`dse`.`store_fov`) AS `store_fov`, SUM(`dse`.`item_fov`) AS `item_fov`, SUM(`dse`.`cart`) AS `cart`, SUM(`dse`.`ds_pv`) AS `ds_pv`, SUM(`dse`.`ds_click`) AS `ds_click`, SUM(`dse`.`ds_charge`) AS `ds_charge`, IFNULL(SUM(`dse`.`ds_click`)/SUM(`dse`.`ds_pv`)*100, 0) AS `ds_ctr`, IFNULL(SUM(`dse`.`ds_charge`)/SUM(`dse`.`ds_click`)/100, 0) AS `ds_click_cost`, IFNULL(SUM(`dse`.`loss`)/SUM(`dse`.`uv`)*100, 0) AS `jump_rate`, IFNULL(SUM(`dse`.`pv`)/SUM(`dse`.`uv`), 0) AS `depth`, IFNULL(SUM(`dse`.`achieve_users`)/SUM(`dse`.`uv`)*100, 0) AS `achieve_rate`, IFNULL(SUM(`dse`.`achieve_payment`)/SUM(`dse`.`achieve_users`), 0) AS `per_customer_transaction`, IFNULL(SUM(`dse`.`achieve_payment`)/(SUM(`dse`.`ds_charge`)/100), 0) AS `roi`, IFNULL(SUM(`dse`.`store_fov`)/SUM(`dse`.`uv`)*100, 0) AS `store_fov_rate`, IFNULL(SUM(`dse`.`item_fov`)/SUM(`dse`.`uv`)*100, 0) AS `item_fov_rate`, IFNULL(SUM(`dse`.`store_fov`+`dse`.`item_fov`)/SUM(`dse`.`uv`)*100, 0) AS `fov_rate`, IFNULL(SUM(`dse`.`cart`)/SUM(`dse`.`uv`)*100, 0) AS `cart_rate` FROM (`diy_plan` AS `p`) RIGHT JOIN `dsp_stats_ad_task_effects` AS `dse` ON dse.date BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND `dse`.`edate` BETWEEN `dse`.`date` AND date_add(`dse`.`date`, interval 0 day) AND dse.plan_id=p.plan_id WHERE `p`.`uid` =  '" . $uid . "' AND `p`.`plat_id` =  '134' AND `p`.`shop_id` =  '" . $shop_id . "' AND `p`.`is_del` =  '0' GROUP BY `p`.`plan_id` ORDER BY SUM(dse.date)desc LIMIT " . intval(($offset - 1) / $num) . "," . $num;
			break;
		case 'unit':
			$count_sql = 'SELECT COUNT(*) AS `count` FROM (`huihe_marketing_system`.`dsp_stats_ad_task_effects` AS `df`) WHERE `df`.`store_id`="' . $shop_id . '" AND `df`.`date` LIKE "' . date('Y-m', time()) . '%" GROUP BY `df`.`date`';
			$sql = "SELECT `u`.`unit_id`, `u`.`unit_name`, `u`.`plan_id`, `u`.`plan_name`, `u`.`type`, `u`.`shop_id`, `u`.`plat_id`, `u`.`plat_name`, `u`.`uid`, `u`.`status`, `u`.`price`, SUM(`dse`.`referer_pv`) AS `referer_pv`, SUM(`dse`.`pv`) AS `pv`, SUM(`dse`.`sv`) AS `uv`, SUM(`dse`.`pat_users`) AS `pat_users`, SUM(`dse`.`pat_trades`) AS `pat_trades`, SUM(`dse`.`pat_payment`) AS `pat_payment`, SUM(`dse`.`achieve_users`) AS `achieve_users`, SUM(`dse`.`achieve_trades`) AS `achieve_trades`, SUM(`dse`.`achieve_payment`) AS `achieve_payment`, SUM(`dse`.`store_fov`) AS `store_fov`, SUM(`dse`.`item_fov`) AS `item_fov`, SUM(`dse`.`cart`) AS `cart`, SUM(`dse`.`ds_pv`) AS `ds_pv`, SUM(`dse`.`ds_click`) AS `ds_click`, SUM(`dse`.`ds_charge`) AS `ds_charge`, IFNULL(SUM(`dse`.`ds_click`)/SUM(`dse`.`ds_pv`)*100, 0) AS `ds_ctr`, IFNULL(SUM(`dse`.`ds_charge`)/SUM(`dse`.`ds_click`)/100, 0) AS `ds_click_cost`, IFNULL(SUM(`dse`.`loss`)/SUM(`dse`.`uv`)*100, 0) AS `jump_rate`, IFNULL(SUM(`dse`.`pv`)/SUM(`dse`.`uv`), 0) AS `depth`, IFNULL(SUM(`dse`.`achieve_users`)/SUM(`dse`.`uv`)*100, 0) AS `achieve_rate`, IFNULL(SUM(`dse`.`achieve_payment`)/SUM(`dse`.`achieve_users`), 0) AS `per_customer_transaction`, IFNULL(SUM(`dse`.`achieve_payment`)/(SUM(`dse`.`ds_charge`)/100), 0) AS `roi`, IFNULL(SUM(`dse`.`store_fov`)/SUM(`dse`.`uv`)*100, 0) AS `store_fov_rate`, IFNULL(SUM(`dse`.`item_fov`)/SUM(`dse`.`uv`)*100, 0) AS `item_fov_rate`, IFNULL(SUM(`dse`.`store_fov`+`dse`.`item_fov`)/SUM(`dse`.`uv`)*100, 0) AS `fov_rate`, IFNULL(SUM(`dse`.`cart`)/SUM(`dse`.`uv`)*100, 0) AS `cart_rate` FROM (`diy_unit` AS `u`) RIGHT JOIN `dsp_stats_ad_task_effects` AS `dse` ON dse.date BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND `dse`.`edate` BETWEEN `dse`.`date` AND date_add(`dse`.`date`, interval 0 day) AND dse.unit_id=u.unit_id WHERE `u`.`uid` =  '" . $uid . "' AND `u`.`plat_id` =  '134' AND `u`.`shop_id` =  '" . $shop_id . "' AND `u`.`is_del` =  '0' GROUP BY `u`.`unit_id` ORDER BY SUM(dse.date)desc LIMIT " . intval(($offset - 1) / $num) . "," . $num;
			break;
		case 'creative':
			$count_sql = 'SELECT COUNT(*) AS `count` FROM (`huihe_marketing_system`.`dsp_stats_ad_task_effects` AS `df`) WHERE `df`.`store_id`="' . $shop_id . '" AND `df`.`date` LIKE "' . date('Y-m', time()) . '%" GROUP BY `df`.`date`';
			$sql = "SELECT `at`.`id`, `at`.`plan_id`, `at`.`plan_name`, `at`.`unit_id`, `at`.`unit_name`, `at`.`borad_id`, `at`.`borad_name`, `at`.`borad_url`, `at`.`kid`, `at`.`k_url`, `at`.`j_url`, `at`.`add_time`, `at`.`pic_path`, `at`.`pic_width`, `at`.`pic_height`, `at`.`status`, `at`.`is_tb`, SUM(`dse`.`referer_pv`) AS `referer_pv`, SUM(`dse`.`pv`) AS `pv`, SUM(`dse`.`sv`) AS `uv`, SUM(`dse`.`pat_users`) AS `pat_users`, SUM(`dse`.`pat_trades`) AS `pat_trades`, SUM(`dse`.`pat_payment`) AS `pat_payment`, SUM(`dse`.`achieve_users`) AS `achieve_users`, SUM(`dse`.`achieve_trades`) AS `achieve_trades`, SUM(`dse`.`achieve_payment`) AS `achieve_payment`, SUM(`dse`.`store_fov`) AS `store_fov`, SUM(`dse`.`item_fov`) AS `item_fov`, SUM(`dse`.`cart`) AS `cart`, SUM(`dse`.`ds_pv`) AS `ds_pv`, SUM(`dse`.`ds_click`) AS `ds_click`, SUM(`dse`.`ds_charge`) AS `ds_charge`, IFNULL(SUM(`dse`.`ds_click`)/SUM(`dse`.`ds_pv`)*100, 0) AS `ds_ctr`, IFNULL(SUM(`dse`.`ds_charge`)/SUM(`dse`.`ds_click`)/100, 0) AS `ds_click_cost`, IFNULL(SUM(`dse`.`loss`)/SUM(`dse`.`uv`)*100, 0) AS `jump_rate`, IFNULL(SUM(`dse`.`pv`)/SUM(`dse`.`uv`), 0) AS `depth`, IFNULL(SUM(`dse`.`achieve_users`)/SUM(`dse`.`uv`)*100, 0) AS `achieve_rate`, IFNULL(SUM(`dse`.`achieve_payment`)/SUM(`dse`.`achieve_users`), 0) AS `per_customer_transaction`, IFNULL(SUM(`dse`.`achieve_payment`)/(SUM(`dse`.`ds_charge`)/100), 0) AS `roi`, IFNULL(SUM(`dse`.`store_fov`)/SUM(`dse`.`uv`)*100, 0) AS `store_fov_rate`, IFNULL(SUM(`dse`.`item_fov`)/SUM(`dse`.`uv`)*100, 0) AS `item_fov_rate`, IFNULL(SUM(`dse`.`store_fov`+`dse`.`item_fov`)/SUM(`dse`.`uv`)*100, 0) AS `fov_rate`, IFNULL(SUM(`dse`.`cart`)/SUM(`dse`.`uv`)*100, 0) AS `cart_rate` FROM ((SELECT * FROM diy_ad_task WHERE uid='" . $uid . "' AND plat_id='134' AND shop_id='" . $shop_id . "' AND is_del='0' GROUP BY unit_id, borad_id) `at`) RIGHT JOIN `dsp_stats_ad_task_effects` AS `dse` ON dse.date BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND `dse`.`edate` BETWEEN `dse`.`date` AND date_add(`dse`.`date`, interval 0 day) AND dse.unit_id=at.unit_id AND dse.ad_task_id=at.borad_id WHERE `at`.`uid` =  '" . $uid . "' AND `at`.`plat_id` =  '134' AND `at`.`shop_id` =  '" . $shop_id . "' AND `at`.`is_del` =  '0' GROUP BY `at`.`unit_id`, `at`.`borad_id` ORDER BY SUM(dse.date)desc LIMIT " . intval(($offset - 1) / $num) . "," . $num;
			break;
		case 'device':
			$count_sql = 'SELECT COUNT(*) AS `count` FROM (`huihe_marketing_system`.`dsp_stats_ad_task_effects` AS `df`) WHERE `df`.`store_id`="' . $shop_id . '" AND `df`.`date` LIKE "' . date('Y-m', time()) . '%" GROUP BY `df`.`date`';
			$sql = "SELECT `dse`.`store_id`, `dse`.`device`, SUM(`dse`.`referer_pv`) AS `referer_pv`, SUM(`dse`.`pv`) AS `pv`, SUM(`dse`.`sv`) AS `uv`, SUM(`dse`.`pat_users`) AS `pat_users`, SUM(`dse`.`pat_trades`) AS `pat_trades`, SUM(`dse`.`pat_payment`) AS `pat_payment`, SUM(`dse`.`achieve_users`) AS `achieve_users`, SUM(`dse`.`achieve_trades`) AS `achieve_trades`, SUM(`dse`.`achieve_payment`) AS `achieve_payment`, SUM(`dse`.`store_fov`) AS `store_fov`, SUM(`dse`.`item_fov`) AS `item_fov`, SUM(`dse`.`cart`) AS `cart`, SUM(`dse`.`ds_pv`) AS `ds_pv`, SUM(`dse`.`ds_click`) AS `ds_click`, SUM(`dse`.`ds_charge`) AS `ds_charge`, IFNULL(SUM(`dse`.`ds_click`)/SUM(`dse`.`ds_pv`)*100, 0) AS `ds_ctr`, IFNULL(SUM(`dse`.`ds_charge`)/SUM(`dse`.`ds_click`)/100, 0) AS `ds_click_cost`, IFNULL(SUM(`dse`.`loss`)/SUM(`dse`.`uv`)*100, 0) AS `jump_rate`, IFNULL(SUM(`dse`.`pv`)/SUM(`dse`.`uv`), 0) AS `depth`, IFNULL(SUM(`dse`.`achieve_users`)/SUM(`dse`.`uv`)*100, 0) AS `achieve_rate`, IFNULL(SUM(`dse`.`achieve_payment`)/SUM(`dse`.`achieve_users`), 0) AS `per_customer_transaction`, IFNULL(SUM(`dse`.`achieve_payment`)/(SUM(`dse`.`ds_charge`)/100), 0) AS `roi`, IFNULL(SUM(`dse`.`store_fov`)/SUM(`dse`.`uv`)*100, 0) AS `store_fov_rate`, IFNULL(SUM(`dse`.`item_fov`)/SUM(`dse`.`uv`)*100, 0) AS `item_fov_rate`, IFNULL(SUM(`dse`.`store_fov`+`dse`.`item_fov`)/SUM(`dse`.`uv`)*100, 0) AS `fov_rate`, IFNULL(SUM(`dse`.`cart`)/SUM(`dse`.`uv`)*100, 0) AS `cart_rate` FROM (`dsp_stats_ad_task_effects` AS `dse`) WHERE `dse`.`store_id` = '" . $shop_id . "' AND `dse`.`date` BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND `dse`.`edate` BETWEEN `dse`.`date` AND date_add(`dse`.`date`, interval 0 day) GROUP BY `dse`.`device` ORDER BY `date` desc LIMIT " . intval(($offset - 1) / $num) . "," . $num;
			break;
		default:
			$count_sql = 'SELECT COUNT(*) AS `count` FROM (`huihe_marketing_system`.`dsp_stats_ad_task_effects` AS `df`) WHERE `df`.`store_id`="' . $shop_id . '" AND `df`.`date` LIKE "' . date('Y-m', time()) . '%" GROUP BY `df`.`date`';
			// $sql = 'SELECT `df`.`date`,`df`.`ds_pv`,`df`.`ds_click`,`df`.`ds_charge` FROM (`huihe_marketing_system`.`dsp_stats_ad_task_effects` AS `df`) WHERE `df`.`store_id`="'.$shop_id.'" AND `df`.`date` LIKE "'.date('Y-m',time()).'%" GROUP BY `df`.`date` ORDER BY `df`.`date` '.$stor.' LIMIT '.intval(($offset-1)/$num).','.$num;

			$sql = "SELECT `dse`.`store_id`, `dse`.`date`, SUM(`dse`.`referer_pv`) AS `referer_pv`, SUM(`dse`.`pv`) AS `pv`, SUM(`dse`.`sv`) AS `uv`, SUM(`dse`.`pat_users`) AS `pat_users`, SUM(`dse`.`pat_trades`) AS `pat_trades`, SUM(`dse`.`pat_payment`) AS `pat_payment`, SUM(`dse`.`achieve_users`) AS `achieve_users`, SUM(`dse`.`achieve_trades`) AS `achieve_trades`, SUM(`dse`.`achieve_payment`) AS `achieve_payment`, SUM(`dse`.`store_fov`) AS `store_fov`, SUM(`dse`.`item_fov`) AS `item_fov`, SUM(`dse`.`cart`) AS `cart`, SUM(`dse`.`ds_pv`) AS `ds_pv`, SUM(`dse`.`ds_click`) AS `ds_click`, SUM(`dse`.`ds_charge`) AS `ds_charge`, IFNULL(SUM(`dse`.`ds_click`)/SUM(`dse`.`ds_pv`)*100, 0) AS `ds_ctr`, IFNULL(SUM(`dse`.`ds_charge`)/SUM(`dse`.`ds_click`)/100, 0) AS `ds_click_cost`, IFNULL(SUM(`dse`.`loss`)/SUM(`dse`.`uv`)*100, 0) AS `jump_rate`, IFNULL(SUM(`dse`.`pv`)/SUM(`dse`.`uv`), 0) AS `depth`, IFNULL(SUM(`dse`.`achieve_users`)/SUM(`dse`.`uv`)*100, 0) AS `achieve_rate`, IFNULL(SUM(`dse`.`achieve_payment`)/SUM(`dse`.`achieve_users`), 0) AS `per_customer_transaction`, IFNULL(SUM(`dse`.`achieve_payment`)/(SUM(`dse`.`ds_charge`)/100), 0) AS `roi`, IFNULL(SUM(`dse`.`store_fov`)/SUM(`dse`.`uv`)*100, 0) AS `store_fov_rate`, IFNULL(SUM(`dse`.`item_fov`)/SUM(`dse`.`uv`)*100, 0) AS `item_fov_rate`, IFNULL(SUM(`dse`.`store_fov`+`dse`.`item_fov`)/SUM(`dse`.`uv`)*100, 0) AS `fov_rate`, IFNULL(SUM(`dse`.`cart`)/SUM(`dse`.`uv`)*100, 0) AS `cart_rate` FROM (`dsp_stats_ad_task_effects` AS `dse`) WHERE `dse`.`store_id` = '" . $shop_id . "' AND `dse`.`date` BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND `dse`.`edate` BETWEEN `dse`.`date` AND date_add(`dse`.`date`, interval 0 day) GROUP BY `dse`.`date` ORDER BY `date` desc LIMIT " . intval(($offset - 1) / $num) . "," . $num;
			break;
		}

		var_dump($sql);
		
		$count_query = $this->db->query($count_sql);

		$count = $count_query && $count_query->num_rows() > 0 ? $count_query->result_array()[0]['count']: 0;
		$query = $this->db->query($sql);
		// var_dump($sql);
		// var_dump($count_sql);
		return $query && $query->num_rows() > 0 ? $query->result_array() : array();
	}
}
