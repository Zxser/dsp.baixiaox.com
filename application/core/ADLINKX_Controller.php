<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | ADLINKX_Controller Controller extended class
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class ADLINKX_Controller extends CI_Controller {
	public $uid;
	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->helper('cookie');
	}

	public function assign($key, $value) {
		$this->cismarty->assign($key, $value);
	}

	public function display($html) {
		$this->cismarty->display($html);
	}

	public function initialization() {
		if (is_array($this->session->userdata()) && ($this->session->has_userdata('uid') || $this->session->has_userdata('name'))) {
			$this->uid = $this->session->userdata('uid');
			$m = $this->uri->segment(3);
			$a = $this->uri->segment(4);
			$data['user']['uid'] = $this->session->userdata('uid') !== '' ? $this->session->userdata('uid') !== '' : 'root';
			$data['user']['username'] = $this->session->userdata('username') !== '' ? $this->session->userdata('username') : '';
			$data['user']['avatar'] = $this->session->userdata('avatar') !== '' ? '/resources/images/avatar/' . $this->session->userdata('avatar') : '/resources/images/photos/loggeduser.png';
			$this->assign('user', $data['user']);
			$this->assign('model',$m);
			$this->assign('action',$a);
		} else {
			$this->_redirect('http://dsp.baixiaox.com/user/login', 'auto ', 301);
		}

	}

	public function _redirect($url,$method = 'auto', $code = 301){
		redirect($url, $method, $code);
	}

	protected function _get($name){
		return $_GET[$name] ? $_GET[$name]:($this->input->get($name) ? $this->input->get($name): null) ;
	}

	protected function _import($file_name) {
		require APPPATH . "libraries/" . $file_name . ".php";
	}

	public function output_json($fl = true, $msg = ''){
		$result = array();
		if($fl){
			$result['code'] = 1;
			$result['msg'] = 'success';
			$result['data'] = $msg;
		}else{
			$result['code'] = 0;
			$result['msg'] = 'error';
			$result['data'] = $msg;
		}
		$this->output
    		->set_content_type('application/json','utf-8')
    		->set_output(json_encode($result));
	}
}