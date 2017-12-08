<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | Home Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class Home extends ADLINKX_Controller {
	public function __construct() {
		parent::__construct();
		$this->initialization();
		$this->load->model('user_model','user');
		$this->load->model('store_model','store');
		$this->load->model('launch_model','launch');
	}

	public function index() {
		$data = array();
		$user = $this->user->get(array('uid' => $this->session->userdata('uid'),'channel_id' => 225, 'isdel' => '0'));
		$store = $this->store->get_all(array('own_id' => $this->session->userdata('uid'), 'is_del' => '0'));
		if($user && !empty($user) && $store && !empty($store)){
			$launch = $this->launch->get_all(array('uid' => $this->session->userdata('uid'),'shop_id' =>$store[0]['shop_id'], 'is_del' => '0'));
		}else{
			$launch = $this->launch->get_all(array('uid' => $this->session->userdata('uid'), 'is_del' => '0'));
		}

		// var_dump($launch);
		$start_launch_num = 0;
		$launch_num = count($launch);
		if($launch_num > 0){
			for($i=0;$i<count($launch);$i++){
				if($launch[$i]['status'] == 1){
					$start_launch_num += 1;
				}
			}
		}else{
			$start_launch_num = 0;
		}
		

		// var_dump($user);
		
		// var_dump($launch);
		// var_dump($store);
		$smarty_cache_id = 'home_index';
		$this->assign('user',$user);
		$this->assign('store',$store);
		$this->assign('launch',$launch);
		$this->assign('start_launch_num',$start_launch_num);
		$this->assign('launch_num',$launch_num);
		$this->display('home/index.html',$smarty_cache_id);
	}
}