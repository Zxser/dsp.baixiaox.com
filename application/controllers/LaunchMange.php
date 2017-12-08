<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | LaunchMange Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class LaunchMange extends ADLINKX_Controller {
	public function __construct() {
		parent::__construct();
		$this->initialization();
		$this->load->model('store_model', 'store');
		$this->load->model('launch_model', 'launch');
		$this->load->model('strategy_model', 'strategy');
		$this->load->model('creative_model', 'creative');
		$this->load->model('loger_model', 'loger');
	}
	public function index() {

	}

	public function add() {
		$add_data = $this->input->post();
		$add_data['uid'] = $this->session->userdata('uid');
		$plan_id = $this->launch->add($add_data);
		if ($plan_id) {
			$log_data = array(
				'uid' => $this->session->userdata('uid'),
				'username' => $this->session->userdata('username'),
				'channel_id' => 224,
				'operation' => '',
				'operate_time' => date('Y-m-d H:i:s', time()),
				'ip' => FN_GET_CLIENT_IP(),
			);
			//添加操作日志
			$this->loger->add_operation_log($log_data);
			$this->session->set_userdata('plan_name', $add_data['plan_name']);
			$this->session->set_userdata('plan_id', $plan_id);
			$this->output_json(true, array('plan_id' => $plan_id));
		} else {
			$this->output_json(false, '');
		}
	}

	public function update() {
		$data = $this->input->post();
		$where = array('plan_id' => $data['plan_id']);
		unset($data['plan_id']);
		$res = $this->launch->update($data, $where);
		if ($res) {
			$this->output_json(true, '');
		} else {
			$this->output_json(false, '');
		}
	}

	public function delete() {
		$where = array();
		$ids = $this->input->post('ids');
		$ids2arr = explode(',', $ids);
		for ($i = 0; $i < count($ids2arr); $i++) {
			$where[$i]['plan_id'] = $ids2arr[$i];
		}
		// 删除投放
		$del = $this->launch->delete($where);
		// 删除策略
		$this->strategy->delete($where);
		// 删除创意
		$this->creative->delete($where);
		if ($del) {
			$this->output_json(true, '');
		} else {
			$this->output_json(false, '');
		}
	}

	public function get() {

	}

	public function lists() {
		$this->get_store_lists();
		$is_ajax = $this->uri->segment(5) ? $this->uri->segment(5) : 0;
		$shop_id = $this->uri->segment(6) ? $this->uri->segment(6) : $this->session->userdata('shop_id');
		$key_words = $this->uri->segment(7) || $this->uri->segment(7) != 0 ? urldecode($this->uri->segment(7)) : '';
		$count = 0;
		$offset = $this->uri->segment(8) || $this->uri->segment(8) != 0 ? $this->uri->segment(8) : 1;
		$num = $this->uri->segment(9) || $this->uri->segment(9) != 0? $this->uri->segment(9) : 20;
		$key = $this->uri->segment(10) || $this->uri->segment(10) != 0? $this->uri->segment(10) : 'plan_create_time';
		$stor = $this->uri->segment(11) || $this->uri->segment(11) != 0? $this->uri->segment(11) : 'DESC';
		$start_date = $this->uri->segment(12) || $this->uri->segment(12) != 0? $this->uri->segment(12) : date('Y-m-d', time());
		$end_date = $this->uri->segment(13) || $this->uri->segment(13) != 0? $this->uri->segment(13) : date('Y-m-d', time());
		$fields = '*';
		$where = array();
		$where['uid'] = $this->session->userdata('uid');
		$where['is_del'] = '0';
		if ($shop_id) {
			$where['shop_id'] = $shop_id;
		}
		if ($key_words) {
			$where['plan_name'] = $key_words;
		}
		$where['start_date'] = $start_date;
		$where['end_date'] = $end_date;
		// var_dump($where);
		$result = $this->launch->lists($where, $num, $offset, $key, $stor, $fields, $count);
		// var_dump($result);
		for ($i = 0; $i < count($result); $i++) {
			$result[$i]['ctr'] = sprintf("%.2f", $result[$i]['ctr']);
			$result[$i]['charge'] = sprintf("%.2f", ($result[$i]['charge'] / 100));
			$result[$i]['click_cost'] = sprintf("%.2f", ($result[$i]['click_cost'] / 100));
		}
		if ($is_ajax) {
			$this->output_json(true, array('count' => ceil($count / $num), 'current' => $offset, 'num' => $num, 'list' => $result));
		} else {
			$this->assign('result', $result);
			$this->assign('count', ceil($count / $num));
			$this->assign('current', $offset);
			$this->assign('num', $num);
			$this->display('launch/lists.html');
		}
	}

	public function get_store_lists() {
		$count = 0;
		$store_lists = $this->store->lists(array('own_id' => $this->session->userdata('uid'), 'start_date' => date('Y-m-d', time()),
			'end_date' => date('Y-m-d', time())), 20, 1, 'update_time', 'desc', '*', $count);
		if ($store_lists && !empty($store_lists)) {
			$this->session->set_userdata('shop_id', $store_lists[0]['shop_id']);
		}
		$this->assign('store_lists', $store_lists);
	}

	public function status() {
		$FB = null;
		$ids = $this->input->post('ids');
		$action = $this->input->post('action');
		$ids2arr = explode(',', $ids);
		for ($i = 0; $i < count($ids2arr); $i++) {
			$launch_status = $this->launch->update(array('status' => ($action == 'start' ? 1 : 0)), array('plan_id' => $ids2arr[$i]));
			$unit_status = $this->strategy->update(array('status' => ($action == 'start' ? 1 : 0)), array('plan_id' => $ids2arr[$i]));
			$creative_status = $this->creative->update(array('status' => ($action == 'start' ? 1 : 0)), array('plan_id' => $ids2arr[$i]));
			$FB = $launch_status = $unit_status = $creative_status;
		}
		if ($FB) {
			$this->output_json(true, '');
		} else {
			$this->output_json(false, '');
		}
	}
}