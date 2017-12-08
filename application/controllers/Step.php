<?php

class Step extends ADLINKX_Controller {
	public function __construct() {
		parent::__construct();
		$this->initialization();
		$this->load->model('store_model','store');
		$this->load->model('launch_model','launch');
		$this->load->model('strategy_model','strategy');
		$this->load->model('creative_model','creative');
	}

	public function edit(){

		$count = 0;
		$step = $this->uri->segment(5);
		$step2arr = explode('_', $step);
		switch($step2arr[0]){
			case 2:
				if($step2arr[1] == 1){
					$plan_id = $this->uri->segment(6);
					$this->assign('but_fn','add');
					$this->assign('plan_id',$plan_id);
					$strategy = array();
					$strategy['shop_id'] = '';
					$strategy['plan_id'] = '';
					$strategy['unit_name'] = '';
					$strategy['price'] = '';
					$strategy['tags_value'] = '';
					$strategy['unit_id'] = '';

				}else{
					$unit_id = $this->uri->segment(6);
					$strategy = $this->strategy->get(array('unit_id' => $unit_id));
					$this->assign('but_fn','update');
					$this->assign('unit_id',$unit_id);
					if(empty($strategy)){
						$strategy['unit_name'] = '';
						$strategy['price'] = '';
						$strategy['tags_value'] = '';
						$strategy['unit_id'] = '';
					}
				}
				$this->assign('strategy',$strategy);
			break;
			case 3:
				if($step2arr[1] == 1){
					$creative = array();
					$unit_id = $this->uri->segment(6);
					$this->assign('unit_id',$unit_id);
					$this->assign('but_fn','add');
				}else{
					$id = $this->uri->segment(6);
					$creative = $this->creative->get(array('id' => $id));
					// var_dump($creative);
					$this->assign('but_fn','update');
				}
				//borad_url,pic_path,pic_height,pic_width,pic_size,gxb_monitor_url
				if(empty($creative)){
					$creative['id'] = '';
					$creative['borad_name'] = '';
					$creative['borad_url'] = '';
					$creative['pic_path'] = '';
					$creative['pic_extension'] = '';
					$creative['pic_width'] = '';
					$creative['pic_height'] = '';
					$creative['pic_size'] = '';
					$creative['gxb_monitor_url'] = '';
				}
				$this->assign('creative',$creative);
			break;
			default:
				if($step2arr[1] == 1){
					$shop_id = $this->uri->segment(6);
					$this->assign('shop_id',$shop_id);
					$this->assign('but_fn','add');
				}else{
					$this->assign('but_fn','update');
				}
				$plan_id = $this->uri->segment(6);
				$this->assign('plan_id',$plan_id);
				$launch = $this->launch->get(array('plan_id' => $plan_id));
				if(empty($launch)){
					$launch['plan_name'] = '';
					$launch['plan_id'] = '';
					$launch['budget'] = '';
					$launch['startdate'] = '';
					$launch['enddate'] = '';
					$launch['device'] = 0;
					$launch['tags_value'] = '';
				}
				// var_dump($launch);
				$this->assign('launch',$launch);
			break;
		}
		$store_lists = $this->store->lists(array('own_id' =>$this->session->userdata('uid'), 'start_date' => date('Y-m-d',time()),
			'end_date' => date('Y-m-d',time())),20,1,'shop_id','desc','*',$count);
		$this->assign('store_lists',$store_lists);
		$this->assign('step',$step);
		$this->display('step/edit.html');
	}
}