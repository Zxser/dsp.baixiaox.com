<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | Store Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date 2017-01-07
 * |--------------------------------------------------------------
 */
class Store extends ADLINKX_Controller{
	public function __construct() {
		parent::__construct();
		$this->initialization();
		$this->load->model('store_model','store');
		$this->load->model('user_model','user');
		$this->load->model('launch_model','launch');
		$this->load->model('strategy_model','strategy');
		$this->load->model('creative_model','creative');
	}

	public function add(){
		$store = array('shop_title' => '','website' => '');
		$this->assign('fun','add');
		$this->assign('store',$store);
		$this->display('store/add_store.html');
	}

	public function add_to(){
		$data = $this->input->post();
		$status = $this->store->add($data);
		if($status){
			$this->output_json(true,'');
		}else{
			$this->output_json(false,'');
		}
	}

	public function edit(){
		$shop_id = $this->uri->segment(5);
		$store = $this->get(array('shop_id' => $shop_id));
		// var_dump($store);
		$this->assign('fun','edit');
		$this->assign('store',$store);
		$this->display('store/add_store.html');
	}

	public function get($where){
		return $this->store->get($where);
	}

	public function lists(){
		$this->get_user();
		$is_ajax = $this->uri->segment(5) ? $this->uri->segment(5) : 0;
		$count = 0;
		$where =  array(
			'own_id' => $this->session->userdata('uid'),
			'start_date' => date('Y-m-d',time()),
			'end_date' => date('Y-m-d',time())
		);
		$offset = $this->uri->segment(7) != '' ? $this->uri->segment(7) : 1 ;
		$num = $this->uri->segment(8) != '' ? $this->uri->segment(8) : 20 ;
		$stor = $this->uri->segment(9) != '' ? $this->uri->segment(9) : 'DESC' ;
		$key = $this->uri->segment(10) != '' ? $this->uri->segment(10) : 'shop_id' ;
		$fields = '*';
		$store_lists = $this->store->lists($where, $num, $offset, $key, $stor, $fields, $count);
		if(is_array($store_lists) && count($store_lists) > 0){
			for($i=0;$i<count($store_lists);$i++){
				$store_lists[$i]['store_money'] = sprintf("%.2f",$store_lists[$i]['store_money']);
				$store_lists[$i]['account_money'] = sprintf("%.2f",$store_lists[$i]['account_money']);
				$store_lists[$i]['charge_yesterday'] = sprintf("%.2f",$store_lists[$i]['charge_yesterday']);
				$store_lists[$i]['charge_today'] = sprintf("%.2f",$store_lists[$i]['charge_today']);
				$store_lists[$i]['agent_charge'] = sprintf("%.2f",$store_lists[$i]['agent_charge']);
			}
		}
		// var_dump($store_lists);
		if($is_ajax){
			$this->output_json(true,array(
				'count' => ceil($count/$num),
				'current' => $offset,
				'num' => $num,
				'list' => $store_lists
			));
		}else{
			$this->assign('store_lists',$store_lists);
			$this->assign('count',ceil($count/$num));
			$this->assign('current',$offset);
			$this->assign('num',$num);
			$this->display('store/store.html');
		}
		
	}

	public function delete(){
		$shop_id = $this->uri->segment(3) ? $this->uri->segment(3) : $this->input->post('shop_ids');
		$shop_id2arr = explode(',', $shop_id);
		$where = array();
		for($i=0;$i<count($shop_id2arr);$i++){
			$where[$i]['shop_id'] = $shop_id2arr[$i];
		}
		// var_dump($where);
		// exit;
		// 删除店铺 所有删除操作都是只是逻辑删除，并非物理删除
		$status = $this->store->delete($where);
		//删除该店铺下所有计划
		$this->launch->delete($where);
		//删除该店铺下所有策略
		$this->strategy->delete($where);
		//删除该店铺下所有创意
		$this->creative->delete($where);
		if($status){
			$this->output_json(true,'');
		}else{
			$this->output_json(false,'');
		}
	}

	public function update(){

	}

	public function alloc_quota(){
		$data = $this->input->post();
		$store_money_up = $this->store->update_money(array('money' => $data['value']),array('shop_id' => $data['shop_id'],'uid' => $data['uid'], 'shop_title' => $data['shop_title']));
		if($store_money_up){
			$this->output_json(true,'');
		}else{
			$this->output_json(false,'');
		}
	}

	public function get_user(){
		$user = $this->user->get(array('uid' => $this->session->userdata('uid')));
		$this->assign('user',$user);
	}

	public function check_money(){
		$shop_id = $this->uri->segment(3);
		$money = $this->store->get(array('shop_id' => $shop_id, 'own_id' => $this->session->userdata('uid')),'money');
		if($money && count($money) > 0){
			$this->output_json(true,$money);
		}else{
			$this->output_json(false,'');
		}
	}

}