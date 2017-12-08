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
class Report extends ADLINKX_Controller {
	public function __construct() {
		parent::__construct();
		$this->initialization();
		$this->load->model('store_model','store');
	}

	public function index() {

	}

	public function add() {

	}

	public function update() {

	}

	public function delete() {

	}

	public function get() {

	}

	public function lists() {
		$store = $this->store->get_all(array('own_id' => $this->session->userdata('uid'), 'is_del'=> '0'));
		// var_dump($store);
		$this->assign('store',$store);
		$this->display('report/report.html');
	}
}