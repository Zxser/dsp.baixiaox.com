<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | CreativeTemplate Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class CreativeTemplate extends ADLINKX_Controller {
	public function __construct() {
		parent::__construct();
		$this->initialization();
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
		$this->display('creative/temp_list.html');
	}
}