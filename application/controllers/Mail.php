<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | Mail Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date 2017-01-07
 * |--------------------------------------------------------------
 */
class Mail extends ADLINKX_Controller {
	public function __construct() {
		parent::__construct();
		$this->initialization();
	}

	public function index() {

	}

	public function get() {

	}

	public function add() {

	}

	public function update() {

	}

	public function lists() {
		$this->display('mail/list.html');
	}

	public function read() {
		$this->display('mail/read.html');
	}

	public function compose() {
		$this->display('mail/compose.html');
	}
}