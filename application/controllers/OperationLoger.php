<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | LaunchReport Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class OperationLoger extends ADLINKX_Controller {
	public function __construct() {
		parent::__construct();
		$this->initialization();
		$this->load->model('loger_model', 'loger');
	}

	public function index() {
		$count = 0;
		$data = array();
		$where = array('uid' => $this->uid);
		$offset = $this->uri->segment(5) ? $this->uri->segment(5): 1;
		$num = $this->uri->segment(6) ? $this->uri->segment(6): 20;
		$key = $this->uri->segment(7) ? $this->uri->segment(7): 'time';
		$sort = $this->uri->segment(8) ? $this->uri->segment(8): 'DESC';
		$fields = array('*');
		$result = $this->getAll($where, $num, $offset, $key, $sort, $fields, $count);
		if($result && !empty($result) && count($result)>0){
			for($i=0;$i<count($result);$i++){
				$result[$i]['money'] = sprintf("%01.2f", $result[$i]['money']);
				$result[$i]['user_money'] = sprintf("%01.2f", $result[$i]['user_money']);
			}
		}
		$loger_lists = array(
			'count' => $count > $num ? ceil($count/$num) : 1,
			'current' => $offset,
			'num' => $num,
			'data' => $result,
		);
		$this->assign('logers', $loger_lists);
		$this->display('loger/lists.html');
	}

	public function add() {

	}

	public function update() {

	}

	public function delete() {
		$where = array('uid' => ($this->uri->segment(3) ? $this->uri->segment(3) : $this->session->userdata('uid')), 'id' => $this->uri->segment(4));
		$res = $this->loger->delete($where);
		$this->output_json($res);
	}

	public function get() {

	}

	public function lists() {
		// $this->getAll($where, $num, $offset, $key, $sort, $fields);
	}

	private function getAll($where, $num, $offset, $key, $sort, $fields, $count) {
		$lists = $this->loger->lists($where, $num, $offset, $key, $sort, $fields, $count);
		return $lists && is_array($lists) && !empty($lists) ? $lists : array();
	}
}