<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | Ajax_api Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class Ajax_api extends ADLINKX_Controller {
	public function __construct() {
		parent::__construct();
		$this->initialization();
		$this->load->model('store_model', 'store');
		$this->load->model('user_model', 'user');
		$this->load->model('launch_model', 'launch');
		$this->load->model('Dsp_stats_ad_task_model', 'dsatm');
		$this->load->model('api_model', 'api');
	}

	public function store() {

		$type = $this->uri->segment(3);
		// var_dump($type);
		$result = array();
		switch ($type) {
		case 'lists':
			$where = array();
			$count = 0;
			///ajax_api/store/lists/start_date/end_data/key_words/offset/num/key/stor
			$start_date = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d', time());
			$end_start = $this->uri->segment(5) ? $this->uri->segment(5) : date('Y-m-d', time());
			$key_words = $this->uri->segment(6) ? urldecode($this->uri->segment(6)) : '';
			$offset = $this->uri->segment(7) ? $this->uri->segment(7) : 1;
			$num = $this->uri->segment(8) ? $this->uri->segment(8) : 20;
			$key = $this->uri->segment(9) ? $this->uri->segment(9) : 'shop_id';
			$stor = $this->uri->segment(10) ? $this->uri->segment(10) : 'DESC';
			$fields = '*';
			$where['start_date'] = $start_date;
			$where['end_date'] = $end_start;
			if (isset($key_words) && !empty($key_words)) {
				// is_numeric — 检测变量是否为数字或数字字符串
				if (preg_match('/^\d+$/i', $key_words)) {
					$where['shop_id'] = $key_words;
				} else {
					$where['shop_title'] = $key_words;
				}
			}
			$where['own_id'] = $this->session->userdata('uid');
			$store_lists = $this->store->lists($where, $num, $offset, $key, $stor, $fields, $count);
			if (count($store_lists) > 0) {
				for ($i = 0; $i < count($store_lists); $i++) {
					$store_lists[$i]['store_money'] = sprintf("%.2f", $store_lists[$i]['store_money']);
					$store_lists[$i]['account_money'] = sprintf("%.2f", $store_lists[$i]['account_money']);
					$store_lists[$i]['charge_yesterday'] = sprintf("%.2f", $store_lists[$i]['charge_yesterday']);
					$store_lists[$i]['charge_today'] = sprintf("%.2f", $store_lists[$i]['charge_today']);
					$store_lists[$i]['agent_charge'] = sprintf("%.2f", $store_lists[$i]['agent_charge']);
				}
			}
			$this->output_json(true, $store_lists);
			break;
		case 'get':
			$shop_id = $this->uri->segment(4);
			$store = $this->store->get(array('shop_id' => $shop_id, 'own_id' => $this->session->userdata('uid')));
			$this->output_json(true, $store);
			break;
		case 'delete':
			break;
		case 'update':
			break;
		default:
			break;
		}
	}

	public function user() {

		$model = $this->uri->segment(3);
		switch ($model) {
		case 'get':
			$user = $this->user->get(array('uid' => $this->session->userdata('uid')));
			$this->output_json(true, $user);
			break;
		default:
			break;
		}
	}

	public function launch() {

		$model = $this->uri->segment(3);
		switch ($model) {
		case 'get_all':
			$launch_num = 0;
			$start_launch_num = 0;
			$shop_id = $this->uri->segment(4);
			$launchs = $this->launch->get_all(array('shop_id' => $shop_id, 'uid' => $this->session->userdata('uid')));
			$launch_num = count($launchs);
			if ($launch_num > 0) {
				for ($i = 0; $i < $launch_num; $i++) {
					if ($launchs[$i]['status'] == 1) {
						$start_launch_num++;
					}
				}
			}
			$this->output_json(true, array(
				'start_launch' => $start_launch_num,
				'launch_num' => $launch_num,
				'list' => $launchs,
			));

			break;
		default:
			break;
		}
	}

	public function query_list() {
		$type = $this->uri->segment(3) ? $this->uri->segment(3) : '';
		$shop_id = $this->uri->segment(4) ? $this->uri->segment(4) : '';
		// $shop_id = '288230376259247377';
		$start_date = $this->uri->segment(5) ? $this->uri->segment(5) : '';
		// $start_date = '2017-08-01';
		$end_date = $this->uri->segment(6) ? $this->uri->segment(6) : '';
		// $end_date = '2017-08-31';
		$format = $this->uri->segment(7) ? $this->uri->segment(7) : 'chart';
		$metric = $this->uri->segment(8) ? str_replace('ds_', '', $this->uri->segment(8)) : 'pv';
		$offset = $this->uri->segment(9) ? $this->uri->segment(9) : 1;
		$num = $this->uri->segment(10) ? $this->uri->segment(10) : 20;
		$key = $this->uri->segment(11) ? $this->uri->segment(11) : 'id';
		$stor = $this->uri->segment(12) ? $this->uri->segment(12) : 'DESC';
		$fields = '*';
		$count = 0;
		$data = array();
		if ($metric == 'pv') {
			$legend = array('展现量');
			$keys = array('pv');
		} elseif ($metric == 'click') {
			$legend = array('点击');
			$keys = array('click');
		} elseif ($metric == 'charge') {
			$legend = array('总消耗');
			$keys = array('charge');
		} else {
			$legend = array('展现量', '点击', '总消耗');
			$keys = array('pv', 'click', 'charge');

		}
		if ($type == 'week') {
			$fields = ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日'];
			$type = 'date';
		} elseif ($type == 'month') {
			$fields = ['01月份', '02月份', '03月份', '04月份', '05月份', '06月份', '07月份', '08月份', '09月份', '10月份', '11月份', '12月份'];
			$type = 'date';
			$result = $this->api->query_list($type, $shop_id, $start_date, $end_date, $format, $metric, $offset, $num, $key, $stor, $fields, $count);
			// var_dump($result);
			// $tmp = array();
			for ($i = 0; $i < count($keys); $i++) {
				$tmp['name'] = $legend[$i];
				$tmp['type'] = 'line';
				$tmp['stack'] = '总量';
				$tmp['data'] = $this->get_format_data($fields, $type, $keys[$i], $metric, $result);
				array_push($data, $tmp);
			}
		} else {
			$fields = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];
			$type = 'hour';
			$result = $this->api->query_list($type, $shop_id, $start_date, $end_date, $format, $metric, $offset, $num, $key, $stor, $fields, $count);
			// var_dump($result);
			$tmp = array();
			for ($i = 0; $i < count($keys); $i++) {
				$tmp['name'] = $legend[$i];
				$tmp['type'] = 'line';
				$tmp['stack'] = '总量';
				$tmp['data'] = $this->get_format_data($fields, $type, $keys[$i], $metric, $result);
				array_push($data, $tmp);
			}
		}
		$this->output_json(true, array(
			'legend' => $legend,
			'fields' => $fields,
			'data' => $data,
		));
	}

	public function get_format_data($fields, $type, $key, $metric, $data) {
		$tmp = array();
		$arr = $this->tow_arr_to_arr($type, $key, $data);
		// var_dump($arr);
		$num = 0;
		if ($type == 'week') {
			$num = 7;
		} elseif ($type == 'date') {
			$num = 13;
			for ($i = 1; $i < $num; $i++) {
				$a = '0' . $i;
				if (!empty($arr[$a])) {
					array_push($tmp, intval($arr[$a]));
				} else {
					array_push($tmp, 0);
				}
			}
		} else {
			$num = 24;
			for ($i = 0; $i < $num; $i++) {
				if (!empty($arr[$i])) {
					array_push($tmp, intval($arr[$i]));
				} else {
					array_push($tmp, 0);
				}
			}
		}

		return $tmp;
	}

	public function tow_arr_to_arr($type, $key, $data) {
		$tmp = array();
		for ($i = 0; $i < count($data); $i++) {
			$tmp[$data[$i][$type]] = $data[$i][$key];
		}
		return $tmp;
	}

	public function dsp_satef() {
		$type = $this->uri->segment(3) ? $this->uri->segment(3) : '';
		$shop_id = $this->uri->segment(4) ? $this->uri->segment(4) : '';
		// $shop_id = '288230376259247377';
		$start_date = $this->uri->segment(5) ? $this->uri->segment(5) : date('Y-m-d', time());
		$end_date = $this->uri->segment(6) ? $this->uri->segment(6) : date('Y-m-d', time());
		$format = $this->uri->segment(7) ? $this->uri->segment(7) : 'chart';
		$metric = $this->uri->segment(8) ? $this->uri->segment(8) : 'ds_pv#ds_click';
		$offset = $this->uri->segment(9) ? $this->uri->segment(9) : 1;
		$num = $this->uri->segment(10) ? $this->uri->segment(10) : 20;
		$key = $this->uri->segment(11) ? $this->uri->segment(11) : 'id';
		$stor = $this->uri->segment(12) ? $this->uri->segment(12) : 'DESC';
		$fields = '*';
		$count = 0;
		$result = $this->api->dsp_satef($type, $shop_id, $start_date, $end_date, $format, $metric, $offset, $num, $key, $stor, $fields, $count);
		$field = array();
		$tmp = array();
		$data = array();
		$key = explode('-',$metric);
		// for($i=0;$i<count($keys);$i++){
		// 		$tmp['name'] = $legend[$i];
		// 		$tmp['type'] = 'line';
		// 		$tmp['stack'] = '总量';
		// 		$tmp['data'] = $this->get_format_data($fields,$type,$keys[$i],$metric,$result);
		// 		array_push($data,$tmp);
		// 	}

		if ($metric == 'ds_pv-ds_charge') {
		    $legend = array('展现量','总消耗');
		} elseif ($metric == 'ds_pv-ds_click') {
		    $legend = array('展现量', '点击数');
		} elseif ($metric == 'ds_pv-ds_ctr') {
		    $legend = array('展现量', '点击率');
		} elseif ($metric == 'ds_click-ds_ctr') {
		    $legend = array('点击数', '点击率');
		} elseif ($metric == 'ds_click-ds_charge') {
		    $legend = array('点击数', '总消耗');
		} elseif($metric== 'ds_ctr-ds_charge'){
		    $legend = array('点击率', '总消耗');
		}else {
		//pv_click
		    $legend = array('展现量', '点击数');
		}

// 		if ($metric == 'pv_crt' || $metric == 'crt_pv') {
// 			$legend = array('展现量', '点击率');
// 			$key = array('ds_pv', 'ds_crt');
// 		} elseif ($metric == 'pv_charge' || $metric == 'charge_pv') {
// 			$legend = array('展现量', '总消耗');
// 			$key = array('ds_pv', 'ds_charge');
// 		} elseif ($metric == 'click_crt' || $metric == 'crt_click') {
// 			$legend = array('点击', '点击率');
// 			$key = array('ds_click', 'ds_crt');
// 		} elseif ($metric == 'click_charge' || $metric == 'charge_click') {
// 			$legend = array('点击', '总消耗');
// 			$key = array('ds_click', 'ds_charge');
// 		} elseif ($metric == 'crt_charge' || $metric == 'charge_crt') {
// 			$legend = array('点击率', '总消耗');
// 			$$key = array('ds_crt', 'ds_charge');
// 		} else {
// //pv_click
// 			$legend = array('展现量', '点击');
// 			$key = array('ds_pv', 'ds_click');
// 		}
		// var_dump($result);
		$tmp = array();
		$cache = array();
		$field = $this->dsp_satef_format_fields($result);
		for ($i = 0; $i < count($key); $i++) {
			$tmp['name'] = $legend[$i];
			$tmp['type'] = 'line';
			$tmp['stack'] = '总量';
			$tmp['data'] = $this->dsp_satef_format_data($key[$i], $result);
			array_push($data, $tmp);
		}
		$this->output_json(true, array(
			'legend' => $legend,
			'field' => $field,
			'data' => $data,
		));
	}

	public function dsp_satef_format_data($key, $list) {
		$tmp = array();
		if ($list && count($list) > 0) {
			for ($i = 0; $i < count($list); $i++) {
				array_push($tmp, $list[$i][$key]);
			}
		} else {
			$tmp = array('0', '0', '0');
		}

		return $tmp;
	}

	public function dsp_satef_format_fields($list) {
		$tmp = array();
		$day_1 = date('Y-m-d', time());
		$day_2 = date('Y-m-d', strtotime('-1 days'));
		$day_3 = date('Y-m-d', strtotime('-2 days'));
		if ($list && count($list) > 0) {
			for ($i = 0; $i < count($list); $i++) {
				array_push($tmp, $list[$i]['date']);
			}
		} else {
			$tmp = array($day_1, $day_2, $day_3);
		}

		return $tmp;
	}

	public function get_table_data() {
		$type = $this->uri->segment(3) ? $this->uri->segment(3) : '';
		$shop_id = $this->uri->segment(4) ? $this->uri->segment(4) : '';
		// $shop_id = '288230376259247377';
		$start_date = $this->uri->segment(5) ? $this->uri->segment(5) : date('Y-m-d', time());
		// $start_date = '2017-09-07';
		$end_date = $this->uri->segment(6) ? $this->uri->segment(6) : date('Y-m-d', time());
		// $end_date = '2017-09-13';
		$action = $this->uri->segment(7) ? $this->uri->segment(7) : 0;
		$offset = $this->uri->segment(8) ? $this->uri->segment(8) : 1;
		$num = $this->uri->segment(9) ? $this->uri->segment(9) : 20;
		$key = $this->uri->segment(10) ? $this->uri->segment(10) : 'id';
		$stor = $this->uri->segment(11) ? $this->uri->segment(11) : 'DESC';
		$count = 0;
		$fields = '*';
		$uid = $this->session->userdata('uid');
		// $uid = '107535632';
		$result = $this->api->get_table_data($type, $uid, $shop_id, $start_date, $end_date, $offset, $num, $key, $stor, $fields, $count);
		$res = array();
		$res['count'] = $count ? ceil($count / $num) : $count;
		$res['current'] = $offset;
		$res['num'] = $num;
		switch ($type) {
		case 'plan':
			$res['item'] = array('推广计划', '展现量', '点击数', '点击率', '总消耗');
			$columns = array('plan_name', 'ds_pv', 'ds_click', 'ds_ctr', 'ds_charge');
			$excel_name = '按推广计划查看';
			break;
		case 'unit':
			$res['item'] = array('推广组	', '推广计划', '展现量', '点击数', '点击率	', '总消耗');
			$columns = array('unit_name', 'plan_name', 'ds_pv', 'ds_click', 'ds_ctr', 'ds_charge');
			$excel_name = '按推广组查看';
			break;
		case 'creative':
			$res['item'] = array('创意名称', '推广组', '推广计划', '展现量', '点击数', '点击率', '总消耗');
			$columns = array('borad_name', 'unit_name', 'plan_name', 'ds_pv', 'ds_click', 'ds_ctr', 'ds_charge');
			$excel_name = '按推广创意查看';
			break;
		case 'device':
			$res['item'] = array('终端类型', '展现量', '点击数', '点击率', '总消耗');
			$columns = array('device', 'ds_pv', 'ds_click', 'ds_ctr', 'ds_charge');
			$excel_name = '按设备查看';
			break;
		default:
			$res['item'] = array('日期', '展现量', '点击数', '点击率', '总消耗');
			$columns = array('date', 'ds_pv', 'ds_click', 'ds_ctr', 'ds_charge');
			$excel_name = ' 按天查看';
			break;
		}
		if (is_array($result) && count($result)) {
			for ($i = 0; $i < count($result); $i++) {
				$result[$i]['ds_charge'] = sprintf('%.2f', ($result[$i]['ds_charge'] / 100));
				$result[$i]['ds_ctr'] = round($result[$i]['ds_ctr'], 2);
				$result[$i]['ds_pv'] = number_format($result[$i]['ds_pv']);
			}
			$res['list'] = $result;
		} else {
			$res['list'] = array();
		}
		if ($action) {
			$this->export_data_to_excel($excel_name, $columns, $res['item'], $result);
		} else {
			$this->output_json(true, $res);
		}
	}

	public function export_data_to_excel($excel_name, $columns, $title, $data) {
		$excel_name = date('Y-m-d') . '_' . $excel_name . '.xls';
		$this->download_excel($columns, $title, $data, $excel_name);
	}

	public function download_excel($columns_key, $excel_title, $data, $excel_name) {
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		$this->_import("Export");
		//数据库对应的字段名称
		$field_column_arr = array("referer_pv", "pv", "uv", "sv", "jump_rate", "achieve_trades", "achieve_users", "achieve_payment", "achieve_rate", "per_customer_transaction", "pat_trades", "pat_users", "pat_payment", "store_fov", "item_fov", "cart");
		// $columns_key_arr = explode(",", $excel_title);
		// $result_arr = $this->zuan_model->get_zuan_detail_table($param, $field_column_arr);
		// $result = $result_arr['tabledata'];
		$excel_arr = array();
		$i = 0;
		foreach ($data as $key => $value) {
			foreach ($columns_key as $key2 => $value2) {
				$excel_arr[$i][$value2] = $value[$value2];
			}
			$i++;
		}
		Export::excel($excel_title, $excel_arr, $excel_name);
	}

	public function roi() {
		$this->load->model('store_model', 'store');
		$store_id = $this->uri->segment(3) ? $this->uri->segment(3) : '';
		$start_date = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d', time()) . ' 00:00:00';
		$end_date = $this->uri->segment(5) ? $this->uri->segment(5) : date('Y-m-d H:i:s', time());
		$roi = $this->store->get_roi($store_id, $start_date, $end_date);
		$this->output_json(true, array('roi' => $roi));
	}
}
