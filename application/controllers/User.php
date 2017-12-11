<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | User Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class User extends ADLINKX_Controller {
	private $salt;
	public function __construct() {
		parent::__construct();
		// $this->initialization();
		$this->salt = $this->config->item('salt');
		$this->load->model('user_model', 'user');
		$this->load->model('loger_model', 'loger');
	}

	public function login() {
		$this->display('user/login.html');
	}

	public function register() {
		$this->display('user/register.html');
	}

	public function sign_in() {
		$result = array();
		$input_data = $this->input->post();
		$remember = $input_data['isChecked'];
		unset($input_data['isChecked']);
		$check_status = $this->check_password($input_data);
		if ($check_status && isset($check_status) && !empty($check_status)) {
			unset($check_status['password']);
			$this->session->set_userdata($check_status); //设置session
			$this->session->set_userdata('source_oa_flag', 1);
			$this->session->set_userdata('permissions', 0);
			if ($remember) {
//记住密码
				foreach ($check_status AS $key => $value) {
					//设置一周有效，域名www.adease.com，采用https加密传输cookie，共享javascript的cookie
					set_cookie($key, $value, (time() + 3600 * 24 * 7), 'dsp.adease.com', '/', true, false);
				}
			}
			$this->output_json(true, '');
		} else {
			$this->output_json(false, '');
		}
	}

	public function sigin_up() {
		$input_data = array();
		foreach ($this->input->post() AS $key => $value) {
			array_push($input_data, $value);
		}
		list($account, $passwd, $email, $phone, $isChecked) = $input_data;
		$data = array();
		// $data['uid'] = FN_generator_id();
		// $data['enctype'] = FN_md5_enctype();
		// $data['is_del'] = 0;
		// $data['add_time'] = time();
		// $data['group'] = '';
		// $data['permissions'] = 'rw';
		$result = array();

		if (empty($account)) {
			$result['code'] = 2;
			$result['msg'] = '';
			$result['data'] = '';
		} else {
			$data['name'] = $account;
		}
		if (empty($passwd)) {
			$result['code'] = 2;
			$result['msg'] = '';
			$result['data'] = '';
		} else {
			// $data['password'] = FN_md5_password($passwd, $data['enctype']);
			$data['password'] = $passwd;
		}
		if (empty($email)) {
			$result['code'] = 2;
			$result['msg'] = '';
			$result['data'] = '';
		} else {
			$data['email'] = $email;
		}
		if (empty($phone)) {
			$result['code'] = 2;
			$result['msg'] = '';
			$result['data'] = '';
		} else {
			$data['phone'] = $phone;
		}
		$data['channel_id'] = 228;
		$data['owner'] = '百晓电商';
		// var_dump($data);
		$user_status = $this->user->add($data);
		// $loger_data = array(
		// 	'uid' => $data['uid'],
		// 	'actions' => '用户注册',
		// 	'remarks' => '尊敬的用户[' . $data['name'] . ']:您于' . date('Y-m-d', $data['add_time']) . '注册成功。<a href="https://www.adease.com">www.adease.com</a>',
		// 	'ip' => sprintf("%u", ip2long($_SERVER['REMOTE_ADDR'])),
		// 	'is_del' => 0,
		// 	'timer' => $data['add_time'],
		// 	'group' => '',
		// 	'permissions' => 'rw',
		// );
		// $loger_status = $this->loger->add($loger_data);

		if ($user_status) {
			$this->output_json(true, '');
		} else {
			$this->output_json(false, '');
		}
	}

	public function Verification() {
		$data = $this->input->post();
		if (!empty($data['action']) && $data['action'] == 'qaptcha' && !empty($data['qaptcha_key'])) {
			echo 1;
		} else {
			echo 0;
		}
	}

	public function check_password($data) {
		$where = array('username' => $data['account']);
		$passwd = $data['passwd'];
		$query = $this->user->get($where, array('uid', 'username', 'password'));
		return is_array($query) && !empty($query) ? (
			FN_md5_password_verify($passwd, $query['password'], $this->salt) ? $query : false
		) : false;
	}

	public function ckeckLogin() {
		// var_dump($this->session->userdata('uid'));
		echo $this->session->userdata('uid') != '' || $this->session->userdata('name') != '' ? json_encode(array('code' => 0, 'msg' => true, 'data' => $this->session->userdata())) : json_encode(array('code' => 1, 'msg' => false, 'data' => ''));
	}

	public function sign_out() {
		$sesion_items = array('uid', 'group', 'permissions', 'username', 'avatar', 'user_nick_oa', 'source_oa_flag');
		$this->session->unset_userdata($sesion_items);
		redirect('http://dsp.baixiaox.com', 'auto ', 301);
	}

	public function source_oa_to_sigma() {
		$user_nick_oa = isset($_SESSION['user_nick_oa']) && !empty($_SESSION['user_nick_oa']) ? $_SESSION['user_nick_oa'] : (isset($_GET['user_nick']) && !empty($_GET['user_nick']) ? trim($_GET['user_nick']) : urldecode($this->uri->segment(3)));
		$_SESSION['user_nick_oa'] = "";
		$_SESSION['source_oa_flag'] = 2;
		if ($user_nick_oa) {
			$user_info = $this->user->get(array('username' => $user_nick_oa, 'channel_id' => 228));
			// var_dump($user_info);
			// exit;
			if ($user_info && !empty($user_info)) {
				$this->session->set_userdata('uid', $user_info['uid']);
				$this->session->set_userdata('username', $user_info['username']);
				$this->session->set_userdata('permissions', 0);
				$this->_redirect('http://dsp.baixiaox.com');
			} else {
				$this->_redirect('http://dsp.baixiaox.com/user/login');
			}

			// $this->load->model('User_model');
			// if (($user_info = $this->User_model->check_password($user_nick_oa, "koolma@#$")) !== FALSE) {
			// 	$this->init_user_info($user_info);
			// 	$source_oa_flag = $_SESSION['source_oa_flag'];
			// 	//$source_oa_flag 为1跳转到付费页面 为2为项目主页
			// 	if ($source_oa_flag == 1) {
			// 		$this->_redirect("/user/recharge");
			// 	} elseif ($source_oa_flag == 2) {
			// 		$this->_redirect("/user/home");
			// 	}
			// }
		}
	}
}