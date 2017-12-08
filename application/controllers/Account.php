<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | Account Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date 2017-01-07
 * |--------------------------------------------------------------
 */
class Account extends ADLINKX_Controller {
	public function __construct() {
		parent::__construct();
		$this->initialization();
		$this->load->model('account_model','account');
	}

	public function show() {
		$uid = $this->uid;
	}

	public function mange() {
		$this->display('account/mange.html');
	}

	public function invite_code() {
		$this->display('account/invite_code.html');
	}

	public function info(){
		$user = $this->account->get(array('uid' => $this->uid));
		if($user && !empty($user)){
			$user['money'] = sprintf("%01.2f", $user['money']);
			$account = $user;
		}else{
			$account = array(
				'username' => '',
				'wangwang' => '',
				'money' => '',
				'shop_name' => '',
				'shop_site' => '',
				'company_name' => '',
				'company_addr' => '',
				'contact' => '',
				'email' => '',
				'phone' => '',
				'qq' => '',
			);
		}
		$this->assign('account',$account);
		$this->display('account/info.html');
	}

	public function update(){
		$data = $this->input->post();
		$where = array('uid' => $data['uid']);
		unset($data['uid']);
		$res = $this->account->update($data, $where);
		$this->output_json($res);
	}

	public function recharge(){
		$monery = $this->input->post('money');
		$res = $this->account->recharge(array('money' => trim($monery)),array('uid' => $this->uid));
		$this->output_json($res);
	}
}