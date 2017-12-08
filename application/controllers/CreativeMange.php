<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | CreativeMange Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date
 * |--------------------------------------------------------------
 */
class CreativeMange extends ADLINKX_Controller {
	public function __construct() {
		parent::__construct();
		$this->initialization();
		$this->load->model('strategy_model', 'strategy');
		$this->load->model('creative_model', 'creative');
		$this->load->model('diy_board_model', 'diy_board');
	}
	public function index() {

	}

	public function add() {
		$data = $this->input->post();
		$data['uid'] = $this->session->userdata('uid');
		// var_dump($data);
		$id = $this->creative->add($data);
		if ($id) {
			$this->output_json(true, array('id' => $id));
		} else {
			$this->output_json(false, '');
		}
	}

	public function update() {
		$data = $this->input->post();
		$where['id'] = $data['id'];
		unset($data['id']);
		$status = $this->creative->edit_creative($data, $where);
		if ($status) {
			$this->output_json(true, '');
		} else {
			$this->output_json(false, '');
		}
	}

	public function delete() {
		$where = array();
		$ids = $this->input->post('ids');
		$ids2arr = explode(',', $ids);
		for ($i = 0; $i < count($ids2arr); $i++) {
			$where[$i]['id'] = $ids2arr[$i];
		}
		$del = $this->creative->delete($where);
		if ($del) {
			$this->output_json(true, '');
		} else {
			$this->output_json(false, '');
		}
	}

	public function get() {
		$id = $this->uri->segment(3);
		$creative = $this->creative->get(array('id' => $id));
		if ($creative && !empty($creative)) {
			$this->output_json(true, $creative);
		} else {
			$this->output_json(false, '');
		}
	}

	public function lists() {
		$count = 0;
		$is_ajax = $this->uri->segment(5) ? $this->uri->segment(5) : 0;
		$unit_id = $this->uri->segment(6) && $this->uri->segment(6) != 0 ? $this->uri->segment(6) : '';
		$key_words = $this->uri->segment(7) ? urldecode($this->uri->segment(7)) : '';
		$this->get_unit_list($unit_id);
		$where = array();
		if ($unit_id) {
			$where['unit_id'] = $unit_id;
			$this->assign('unit_id', $unit_id);
		} else {
			$this->assign('unit_id', '');
		}
		if ($key_words) {
			$where['borad_name'] = $key_words;
		}
		$where['uid'] = $this->session->userdata('uid');
		$where['is_del'] = '0';
		$offset = $this->uri->segment(8) || $this->uri->segment(8) != 0 ? $this->uri->segment(8) : 1;
		$num = $this->uri->segment(9) || $this->uri->segment(9) != 0 ? $this->uri->segment(9) : 20;
		$key = $this->uri->segment(10) || $this->uri->segment(10) != 0 ? $this->uri->segment(10) : 'id';
		$stor = $this->uri->segment(11) || $this->uri->segment(11) != 0 ? $this->uri->segment(11) : 'DESC';
		$fields = '*';
		$start_date = $this->uri->segment(12) || $this->uri->segment(12) != 0 ? $this->uri->segment(12) : date('Y-m-d', time());
		$end_date = $this->uri->segment(13) || $this->uri->segment(13) != 0 ? $this->uri->segment(13) : date('Y-m-d', time());
		$where['start_date'] = $start_date;
		$where['end_date'] = $end_date;
		$creative_lists = $this->creative->lists($where, $offset, $num, $key, $stor, $fields, $count);
		// var_dump($creative_lists);
		if ($creative_lists && !empty($creative_lists) && count($creative_lists) > 0) {
			for ($i = 0; $i < count($creative_lists); $i++) {
				$creative_lists[$i]['ext'] = $creative_lists[$i]['pic_path'] && !empty($creative_lists[$i]['pic_path']) ? explode('.', $creative_lists[$i]['pic_path'])[1] : '';
				$creative_lists[$i]['ctr'] = sprintf("%.2f", $creative_lists[$i]['ctr']);
				$creative_lists[$i]['charge'] = sprintf("%.2f", ($creative_lists[$i]['charge'] / 100));
				$creative_lists[$i]['click_cost'] = sprintf("%.2f", ($creative_lists[$i]['click_cost'] / 100));
			}
		}
		if ($is_ajax) {
			$this->output_json(true, array('count' => ceil($count / $num), 'current' => $offset, 'num' => $num, 'list' => $creative_lists));
		} else {
			$this->assign('count', ceil($count / $num));
			$this->assign('current', $offset);
			$this->assign('num', $num);
			$this->assign('creatives', $creative_lists);
			$this->display('creative/list.html');
		}
	}

	public function upload_files() {
		$ext_lists = array('jpg', 'jpeg', 'png', 'gif', 'swf', 'avi', 'wmv', '3gp');
		$upload_dir = '/home/wwwroot/sigma.huihex.dsp/img/board/';
		$logo_path = '/home/wwwroot/sigma.huihex.dsp/img/logo/';
		// $upload_dir = '/home/wwwroot/dsp.baixiaox.com/resources/images/board';
		// $upload_dir = 'C:/web/www/dsp.baixiaox.com/resources/images/board';
		$name = $_FILES['files']['name'];
		$type = $_FILES['files']['type'];
		$tmp_name = $_FILES['files']['tmp_name'];
		$get_img_size = getimagesize($_FILES['files']['tmp_name']);
		$file_width = $get_img_size[0];
		$file_height = $get_img_size[1];
		$error = $_FILES['files']['error'];
		$size = $_FILES['files']['size'];
		$file_name = pathinfo($name, PATHINFO_BASENAME);
		$file_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
		if (!in_array($file_ext, $ext_lists)) {
			$this->output_json(false, '错误的文件类型');
		}
		if ($size > 1024 * 1024) {
			//上传文件不能大于1M
			$this->output_json(false, '上传的文件超过规定的大小');
		}
		if ($error != 0) {
			$this->output_json(false, $error);
		}
		$new_file_name = 'd_' . md5(microtime());

		$new_file_path = $upload_dir . '/' . $new_file_name . '.' . $file_ext;
		$cp_file_path = $upload_dir . '/' . $new_file_name . '_logo.' . $file_ext;
		if (move_uploaded_file($tmp_name, $new_file_path)) {
//move upload file
			exec("cp " . $new_file_path . " " . $cp_file_path); //复制上传的图片做为带_logo的图片
			/**
			 * 生成一张带有汇合营销logo的图片
			 * @var [type]
			 */
			//获取要添加logo的目标图片资源
			$dis_img = imagecreatefromstring(file_get_contents($upload_dir . $new_file_name . "_logo." . $file_ext));
			//获取logo图片资源
			$src_img = imagecreatefromstring(file_get_contents($upload_dir . "new_add.png"));
			//合并成一张图片
			imagecopymerge($dis_img, $src_img, 0, imagesy($dis_img) - imagesy($src_img), 0, 0, imagesx($src_img), imagesy($src_img), 100);
			$image_save_name = $new_file_name . "_logo." . $file_ext;
			if (in_array($file_ext, array('jpg', 'jpe', 'jpeg'))) {
				imagejpeg($dis_img, $logo_path . $image_save_name);
			} elseif (in_array($file_ext, array('png'))) {
				imagepng($dis_img, $logo_path . $image_save_name);
			} elseif (in_array($file_ext, array('gif'))) {
				imagegif($dis_img, $logo_path . $image_save_name);
			}
			//将图片信息保存到数据库中
			$board_id = $this->diy_board->add(array(
				'board_name' => $name,
				'pic_path' => $new_file_name,
				'uid' => $this->session->userdata('uid'),
				'pic_width' => $file_width,
				'pic_height' => $file_height,
				'pic_size' => $file_width . '*' . $file_height,
				'pic_extension' => $file_ext,

			));
			if ($board_id) {
				echo '{"upload_file_name":"' . $new_file_name . '.' . $file_ext . '","ext":"' . $file_ext . '","width":"' . $file_width . '","height":"' . $file_height . '","board_id":' . $board_id . '}';
			}
		} else {
			$this->output_json(false, '移动文件失败');
		}
	}

	public function get_unit_list($unit_id) {
		$where = array();
		$where['uid'] = $this->session->userdata('uid');
		$where['is_del'] = '0';
		if ($unit_id) {
			$where['unit_id'] = $unit_id;
		}
		$units = $this->strategy->get_all($where);
		$this->assign('units', $units);
	}

	public function status() {
		$FB = null;
		$ids = $this->input->post('ids');
		$action = $this->input->post('action');
		$ids2arr = explode(',', $ids);
		for ($i = 0; $i < count($ids2arr); $i++) {
			$FB = $this->creative->update(array('status' => ($action == 'start' ? 1 : 0)), array('id' => $ids2arr[$i]));
		}
		if ($FB) {
			$this->output_json(true, '');
		} else {
			$this->output_json(false, '');
		}
	}
}